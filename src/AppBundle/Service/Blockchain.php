<?php

namespace AppBundle\Service;

use AppBundle\Entity\Blockchain\Wallet;
use AppBundle\Exception\Blockchain\AvailableTransactionIsAbsentException;
use AppBundle\Exception\Blockchain\BlockchainDoesNotExistException;
use AppBundle\Exception\Blockchain\BlockchainException;
use AppBundle\Exception\Blockchain\BlockIsNotValidException;
use AppBundle\Exception\Blockchain\BlockMiningException;
use AppBundle\Exception\Blockchain\BlockProofIsNotValid;
use AppBundle\Model\Blockchain\Block;
use AppBundle\Model\Blockchain\InputOutputDto;
use AppBundle\Model\Blockchain\Transaction;
use AppBundle\Repository\Blockchain\BlockchainRepository;
use AppBundle\Service\Blockchain\BlockService;
use AppBundle\Service\Blockchain\MiningService;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializerInterface;

class Blockchain
{
    /** @var ArrayCollection */
    private $chain;

    /** @var  BlockchainRepository */
    private $blockchainRepository;

    /** @var  BlockService */
    private $blockService;

    /** @var  MiningService */
    private $miningService;

    /**
     * Blockchain constructor.
     * @param SerializerInterface  $serializer
     * @param BlockchainRepository $blockchainRepository
     * @param BlockService         $blockService
     * @param MiningService        $miningService
     */
    public function __construct(
        SerializerInterface $serializer,
        BlockchainRepository $blockchainRepository,
        BlockService $blockService,
        MiningService $miningService
    ) {
        $this->blockchainRepository = $blockchainRepository;
        $this->blockService = $blockService;

        $this->chain = new ArrayCollection();
        $this->miningService = $miningService;

        $this->load();
    }

    /**
     * #refactor - split to methods
     * @param Wallet[] $wallets
     * @return void
     */
    public function checkBlockchain(array $wallets): void
    {
        $chain = $this->getChain();

        $lastBlock = null;
        foreach ($chain as $index => $block) {
            if ($index === 1) {
                $lastBlock = $block;
                // skip genesis block checking
                continue;
            }

            $this->blockService->checkBlock($block);

            $isProofValid = $this->miningService->isProofValid(
                $lastBlock->getProof(),
                $block->getProof(),
                $this->blockService->getTransactionPoolHashString($block->getTransactions())
            );

            if ($isProofValid === false) {
                throw new BlockIsNotValidException('Proof is not valid for block with index: ' . $block->getIndex());
            }

            if ($block->getIndex() - $lastBlock->getIndex() !== 1) {
                throw new BlockIsNotValidException('Block index is not correct');
            }
            if ($block->getTimestamp() < $lastBlock->getTimestamp()) {
                throw new BlockIsNotValidException('Block timestamp is less than previous block timestamp');
            }

            if ($block->getPreviousHash() !== $lastBlock->getHash()) {
                throw new BlockIsNotValidException(
                    'Previous block hash is not valid for block with index: ' . $block->getIndex()
                );
            }

            if (!$this->areAllBlockTransactionsValid($block, $wallets)) {
                throw new BlockIsNotValidException(
                    'Not all transactions of block is valid. Index is: ' . $block->getIndex()
                );
            }

            $lastBlock = $block;
        }
    }

    /**
     * @param Block $block
     * @param array $wallets
     * @return bool
     */
    public function areAllBlockTransactionsValid(Block $block, array $wallets): bool
    {
        $transactions = $block->getTransactions();

        $validTransactions = $this->getValidOnlyPoolTransactions($wallets, $transactions);

        return \count($transactions) === \count($validTransactions);
    }

    /**
     * @param array         $wallets
     * @param Transaction[] $transactions
     * @return array
     */
    public function getValidOnlyPoolTransactions(array $wallets, array $transactions): array
    {
        $validTransactions = [];
        foreach ($transactions as $transaction) {
            if ($this->blockService->isMinerTransaction($transaction)) {
                // #docs there is no normal miner transaction checker
                $validTransactions[] = $transaction;
            }

            if (!isset($wallets[$transaction->getSenderAddress()])) {
                continue;
            }

            if (!isset($wallets[$transaction->getRecipientAddress()])) {
                continue;
            }

            $senderWallet = $wallets[$transaction->getSenderAddress()];

            if (!$this->blockService->isTransactionValid($transaction, $senderWallet)) {
                continue;
            }

            $blockIndex = $transaction->getPreviousBlockIndex();
            $previousBlock = $this->getBlockFromChainByIndex($blockIndex);

            if ($previousBlock === null) {
                continue;
            }

            if ($this->isPreviousTransactionExist($previousBlock->getTransactions(), $transaction)) {
                $validTransactions[] = $transaction;
            }
        }

        return $validTransactions;
    }

    /**
     * @param Wallet[] $wallets
     * @return InputOutputDto[]
     */
    public function checkAndRewriteTransactionPool(array $wallets): array
    {
        $transactions = $this->getTransactionPool();

        $validTransactions = $this->getValidOnlyPoolTransactions($wallets, $transactions);

        // #refactor - do not rewrite if everything is ok
        $this->blockService->rewriteTransactionPool($validTransactions);

        return $validTransactions;
    }

    /**
     * @param Wallet $senderWallet
     * @param Wallet $recipientWallet
     * @param int    $amount
     */
    public function addTransactionToPool(
        Wallet $senderWallet,
        Wallet $recipientWallet,
        int $amount
    ): void {
        $this->throwExceptionIfBlockchainEmpty();

        $senderAddress = $senderWallet->getAddress();

        $availableInput = $this->getAvailableInput($senderAddress, $amount);
        if ($availableInput === null) {
            throw new AvailableTransactionIsAbsentException('Available transaction is absent');
        }

        $this->blockService->addRegularTransactionToPool(
            $senderWallet,
            $recipientWallet->getAddress(),
            $availableInput
        );
    }

    /**
     * @return array
     */
    public function getTransactionPool(): array
    {
        return $this->blockService->getTransactionPool();
    }

    /**
     * @param Wallet $creatorWallet
     * @return void
     */
    public function createBlockchain(Wallet $creatorWallet): void
    {
        $this->load();

        if (!$this->chain->isEmpty()) {
            throw new BlockchainException('Blockchain is already exist.');
        }

        $block = $this->blockService->createGenesisBlock($creatorWallet);
        $this->addBlockToChain($block);
    }

    /**
     * @return ArrayCollection|Block[]
     */
    public function getChain(): ArrayCollection
    {
        return $this->chain;
    }

    /**
     * #refactor
     * @param string $address
     * @return InputOutputDto[]
     */
    public function getAvailableInputs(string $address) : array
    {
        $inputs = [];
        $outputs = [];
        foreach ($this->getChain() as $block) {
            $transactions = $block->getTransactions();

            foreach ($transactions as $transaction) {
                if ($transaction->isSender($address)) {
                    $outputs[$transaction->getPreviousTransactionHash()] =
                        new InputOutputDto($block->getIndex(), $transaction);
                } elseif ($transaction->isRecipient($address)) {
                    $inputs[$transaction->getHash()] = new InputOutputDto($block->getIndex(), $transaction);
                }
            }
        }

        return array_diff_key($inputs, $outputs);
    }

    /**
     * @param Wallet   $minerWallet
     * @param Wallet[] $wallets
     */
    public function mineNewBlock(Wallet $minerWallet, array $wallets): void
    {
        $this->checkAndRewriteTransactionPool($wallets);

        if ($this->blockService->isTransactionPoolEmpty()) {
            throw new BlockMiningException('Transaction pool is empty');
        }

        $this->blockService->addMinerRewardTransaction($minerWallet->getAddress());

        // #refactor - create named methods, ext. for pool and for block
        $poolHash = $this->blockService->getTransactionPoolHashString($this->getTransactionPool());

        $nextProof = $this->miningService->calculateNextProof(
            $this->getPreviousBlockProof(),
            $poolHash
        );

        $this->createNewBlockAndAddToChain($nextProof, $poolHash);
    }

    /**
     * #refactor - method name is not ok
     * @return void
     */
    public function throwExceptionIfBlockchainEmpty(): void
    {
        if ($this->getChain()->isEmpty()) {
            throw new BlockchainDoesNotExistException('Blockchain does not exists');
        }
    }

    /**
     * @return mixed
     */
    private function getPreviousBlockProof()
    {
        return $this->getLastBlock()->getProof();
    }

    /**
     * @param Block $block
     */
    private function addBlockToChain(Block $block): void
    {
        $blockJson = $this->blockService->serializeBlock($block);
        $this->blockchainRepository->insertBlock($block->getIndex(), $blockJson);

        // #refactor
        $this->load();
    }

    /**
     * @return void
     */
    private function load() : void
    {
        $this->chain = new ArrayCollection();
        $blocks = $this->blockchainRepository->getAllBlocks();

        foreach ($blocks as $block) {
            $deserialized = $this->blockService->deserializeBlock($block['block_json']);
            $this->chain[$deserialized->getIndex()] = $deserialized;
        }
    }

    /**
     * @return Block
     */
    private function getLastBlock() : Block
    {
        return $this->chain->last();
    }

    /**
     * @param int $index
     * @return Block|null
     */
    private function getBlockFromChainByIndex(int $index): ?Block
    {
        if (!isset($this->chain[$index])) {
            return null;
        }

        return $this->chain[$index];
    }

    /**
     * @param string $senderAddress
     * @param int    $amount
     * @return InputOutputDto|null
     */
    private function getAvailableInput(string $senderAddress, int $amount): ?InputOutputDto
    {
        $inputs = $this->getAvailableInputs($senderAddress);

        foreach ($inputs as $input) {
            if ($input->isAvailableForAmount($amount)) {
                return $input;
            }
        }

        return null;
    }

    /**
     * @param int    $nextProof
     * @param string $poolHash
     */
    private function createNewBlockAndAddToChain(int $nextProof, string $poolHash): void
    {
        if (!$this->miningService->isProofValid($this->getPreviousBlockProof(), $nextProof, $poolHash)) {
            throw new BlockProofIsNotValid('Block Proof is not valid');
        }

        $lastBlock = $this->getLastBlock();

        $block = $this->blockService->createBlock($lastBlock, $nextProof);

        $this->addBlockToChain($block);
    }

    /**
     * @param Transaction[] $previousTransactions
     * @param Transaction   $transaction
     * @return bool
     */
    private function isPreviousTransactionExist(array $previousTransactions, Transaction $transaction) : bool
    {
        foreach ($previousTransactions as $previousTransaction) {
            if ($previousTransaction->getHash() === $transaction->getPreviousTransactionHash()
                && $previousTransaction->getAmount() === $transaction->getAmount()) {

                return true;
            }
        }

        return false;
    }
}
