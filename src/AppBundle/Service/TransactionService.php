<?php

namespace AppBundle\Service;

use AppBundle\Model\Transaction;

class TransactionService
{
    private const SYSTEM_SENDER_PUBLIC_KEY = '0';
    private const GENESIS_EMISSION = 50;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var array
     */
    private $transactionPool;

    /**
     * TransactionService constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
        $this->transactionPool = [];
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransactionToPool(Transaction $transaction): void
    {
        $this->transactionPool[$transaction->getHash()] = $transaction;
    }

    /**
     * @return void
     */
    public function addGenesisTransaction() : void
    {
        if (!empty($this->transactionPool)) {
            throw new \LogicException('There are already a couple of transactions');
        }

        $recipientPublicKey = $this->repository->getCreatorPublicKey();
        $genesisTransaction = $this->createSystemTransaction($recipientPublicKey, self::GENESIS_EMISSION);

        $this->addTransactionToPool($genesisTransaction);
    }

    /**
     * Genesis emission, miner reward
     * @param string $recipientPublicKey
     * @param float  $amount
     * @return Transaction
     */
    public function createSystemTransaction(string $recipientPublicKey, float $amount) : Transaction
    {
        $transaction = new Transaction(
            self::SYSTEM_SENDER_PUBLIC_KEY,
            '0',
            '0',
            $recipientPublicKey,
            $amount
        );

        $transaction->addTransactionValidations();
    }

    private function addTransactionHashAndSignature()
    {
        // todo
    }
}
