<?php

namespace AppBundle\Service;

use AppBundle\Model\Block;
use AppBundle\Model\Transaction;

class Blockchain
{
    /**
     * @var string
     */
    private $projectDir;

    private $chain = [];
    private $transactions = [];

    private $proofZeros = '0';

    private $systemFakeSenderAddress = '0';
    private $minerReward = 25;

    /**
     * @var TransactionService
     */
    private $transactionService;

    private const GENESIS_BLOCK_INDEX = 1;

    private const GENESIS_PROOF = 5;
    private const GENESIS_EMISSION = 50; // get from settings
    private const CREATOR_ADDRESS = 'creator'; // get from wallets

    /**
     * Blockchain constructor.
     * @param string             $projectDir
     * @param TransactionService $transactionService
     */
    public function __construct(
        string $projectDir,
        TransactionService $transactionService
    ) {
        $this->projectDir = $projectDir;
        $this->transactionService = $transactionService;

        if ($this->load() === true) {
            return;
        }

        $this->transactionService->addGenesisTransaction();

        $this->addTransaction($transaction);

        $block = new Block(
            self::GENESIS_BLOCK_INDEX,
            $this->transactions,
            self::GENESIS_PROOF,
            ''
        );

        $this->chain[] = $block->getAsArray();

        $this->save();

        $this->load();
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[$transaction->getChecksum()] = $transaction->getAsArray();
    }

    /**
     * @param int    $nextProof
     * @param string $minerAddress
     */
    public function createNewBlockAndAddToChain(int $nextProof, string $minerAddress): void
    {
        if (!$this->isProofValid($this->getPreviousBlockProof(), $nextProof)) {
            throw new \RuntimeException('Proof is not valid');
        }

        $this->addMinerRewardTransaction($minerAddress);

        $block = new Block(
            $this->getPreviousBlockIndex() + 1,
            array_values($this->transactions),
            $nextProof,
            $this->getPreviousBlockHash()
        );

        $this->chain[] = $block->getAsArray();
        $this->transactions = [];

        $this->save();
    }

    /**
     * @return int
     */
    public function generateNextProof(): int
    {
        $lastProof = $this->getPreviousBlockProof();

        $nextProof = 0;
        while ($this->isProofValid($lastProof, $nextProof) === false) {
            $nextProof++;
        }

        return $nextProof;
    }

    /**
     * @param string $minerAddress
     */
    private function addMinerRewardTransaction(string $minerAddress): void
    {
        $transaction = new Transaction(
            $this->systemFakeSenderAddress,
            $minerAddress,
            $this->minerReward
        );

        $this->addTransaction($transaction);
    }

    /**
     * @return mixed
     */
    private function getPreviousBlockProof()
    {
        return $this->getLastBlock()['proof'];
    }

    /**
     * @return mixed
     */
    private function getPreviousBlockIndex()
    {
        return $this->getLastBlock()['index'];
    }

    /**
     * @return mixed
     */
    private function getPreviousBlockHash()
    {
        return $this->getLastBlock()['hash'];
    }

    /**
     * Validates the Proof: Does hash(last_proof, proof) contain 4 leading zeroes?
     * @param int $lastProof Previous Proof
     * @param int $proof     Current Proof
     * @return bool True if correct, False if not.
     */
    private function isProofValid($lastProof, $proof) : bool
    {
        $hash = hash('sha256', $lastProof * $proof);

        return substr($hash, -1) === $this->proofZeros;
    }

    /**
     *
     */
    private function save(): void
    {
        file_put_contents($this->getFile(), json_encode($this->chain));
    }

    /**
     * @return bool
     */
    private function load() : bool
    {
        if (!file_exists($this->getFile())) {
            return false;
        }

        $json = file_get_contents($this->getFile());

        $this->transactions = [];
        $this->chain = json_decode($json, true);

        return true;
    }

    /**
     * @return string
     */
    private function getFile(): string
    {
        return $this->projectDir . '/var/blockchain.txt';
    }

    /**
     * @return mixed
     */
    private function getLastBlock()
    {
        $lastIndex = count($this->chain) - 1;

        return $this->chain[$lastIndex];
    }
}
