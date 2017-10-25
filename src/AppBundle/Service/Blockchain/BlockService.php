<?php

namespace AppBundle\Service\Blockchain;

use AppBundle\Entity\Blockchain\Wallet;
use AppBundle\Exception\Blockchain\BlockIsNotValidException;
use AppBundle\Model\Blockchain\Block;
use AppBundle\Model\Blockchain\InputOutputDto;
use AppBundle\Model\Blockchain\Transaction;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class BlockService
{
    // #refactor - move to more generic service
    /** @var int  */
    private $genesisEmission = 50;
    /** @var int  */
    private $genesisBlockIndex = 1;
    /** @var int  */
    private $genesisProof = 1;
    /** @var string  */
    private $genesisBlockPreviousHash = '';
    /** @var int  */
    private $minerReward = 25;

    private const REDIS_KEY_TRANSACTION_POOL = 'transactionPool';

    /** @var  TransactionService */
    private $transactionService;

    /** @var  \Redis */
    private $redis;

    /** @var  SerializerInterface */
    private $serializer;

    /**
     * BlockService constructor.
     * @param TransactionService  $transactionService
     * @param \Redis              $redis
     * @param SerializerInterface $serializer
     */
    public function __construct(
        TransactionService $transactionService,
        \Redis $redis,
        SerializerInterface $serializer
    ) {
        $this->transactionService = $transactionService;
        $this->redis = $redis;

        $this->serializer = $serializer;
    }

    /**
     * @param Block $block
     */
    public function checkBlock(Block $block): void
    {
        $hash = $this->calculateBlockHash($block);

        if ($hash !== $block->getHash()) {
            throw new BlockIsNotValidException('block hash is not valid');
        }
    }

    /**
     * @param Transaction $transaction
     * @param Wallet      $senderWallet
     * @return bool
     */
    public function isTransactionValid(Transaction $transaction, Wallet $senderWallet): bool
    {
        return $this->transactionService->isTransactionValid($transaction, $senderWallet);
    }

    /**
     * @param Wallet         $senderWallet
     * @param string         $recipientAddress
     * @param InputOutputDto $availableInput
     */
    public function addRegularTransactionToPool(
        Wallet $senderWallet,
        string $recipientAddress,
        InputOutputDto $availableInput
    ): void {

        $transaction = $this->transactionService->createRegularTransaction(
            $senderWallet,
            $recipientAddress,
            $availableInput
        );

        $this->addTransactionToPool($transaction);
    }

    /**
     * @param string $data
     * @return Block
     */
    public function deserializeBlock(string $data) : Block
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->serializer->deserialize($data, Block::class, 'json');
    }

    /**
     * @param Block $block
     * @return string
     */
    public function serializeBlock(Block $block) : string
    {
        return $this->serializer->serialize(
            $block,
            'json',
            SerializationContext::create()->setGroups(['Default', 'save'])
        );
    }

    /**
     * @param Block $block
     * @return string
     */
    public function serializeBlockForHash(Block $block) : string
    {
        return $this->serializer->serialize($block, 'json', SerializationContext::create()->setGroups(['Default']));
    }

    /**
     * @param string $minerAddress
     */
    public function addMinerRewardTransaction(string $minerAddress): void
    {
        $transaction =
            $this->transactionService->createSystemTransaction($minerAddress, $this->minerReward);

        $this->addTransactionToPool($transaction);
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransactionToPool(Transaction $transaction): void
    {
        $data = $this->transactionService->serializeTransaction($transaction);
        $this->redis->hSet(self::REDIS_KEY_TRANSACTION_POOL, $transaction->getHash(), $data);
    }

    /**
     * #refactor - maybe move to miningService
     * @param Transaction[] $transactions
     * @return string
     */
    public function getTransactionPoolHashString(array $transactions): string
    {
        $result = '';
        foreach ($transactions as $transaction) {
            $result .= $transaction->getHash();
        }

        return hash('sha256', $result);
    }

    /**
     * @param Transaction[] $transactions
     */
    public function rewriteTransactionPool(array $transactions): void
    {
        $this->deleteTransactionPool();

        // #refactor - all at once not one by one
        foreach ($transactions as $transaction) {
            $this->addTransactionToPool($transaction);
        }
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    public function isMinerTransaction(Transaction $transaction): bool
    {
        return $this->transactionService->isMinerTransaction($transaction);
    }

    /**
     * @return bool
     */
    public function isTransactionPoolEmpty() : bool
    {
        $data = $this->getTransactionPool();

        return empty($data);
    }

    /**
     * @return Transaction[]
     */
    public function getTransactionPool() : array
    {
        $transactionPool = $this->redis->hGetAll(self::REDIS_KEY_TRANSACTION_POOL);

        $result = [];
        foreach ($transactionPool as $item) {
            $result[] = $this->transactionService->deserializeTransaction($item);
        }

        return $result;
    }

    /**
     * @param Block $lastBlock
     * @param int   $proof
     * @return Block
     */
    public function createBlock(Block $lastBlock, int $proof) : Block
    {
        $block = new Block(
            $lastBlock->getIndex() + 1,
            $proof,
            $lastBlock->getHash(),
            $this->getTransactionPool()
        );

        $this->setBlockHash($block);

        $this->deleteTransactionPool();

        return $block;
    }

    /**
     * @param Wallet $creatorWallet
     * @return Block
     */
    public function createGenesisBlock(Wallet $creatorWallet) : Block
    {
        $transaction = $this->transactionService->createSystemTransaction(
            $creatorWallet->getAddress(),
            $this->genesisEmission
        );

        $block = new Block(
            $this->genesisBlockIndex,
            $this->genesisProof,
            $this->genesisBlockPreviousHash,
            [
                $transaction,
            ]
        );

        $this->setBlockHash($block);
        $this->deleteTransactionPool();

        return $block;
    }

    /**
     * @param Block $block
     * @return void
     */
    private function setBlockHash(Block $block): void
    {
        $hash = $this->calculateBlockHash($block);
        $block->setHash($hash);
    }

    /**
     * @param Block $block
     * @return string
     */
    private function calculateBlockHash(Block $block) : string
    {
        $serialized = $this->serializeBlockForHash($block);

        return hash('sha256', $serialized);
    }

    /**
     * @return void
     */
    private function deleteTransactionPool() : void
    {
        $this->redis->del(self::REDIS_KEY_TRANSACTION_POOL);
    }
}
