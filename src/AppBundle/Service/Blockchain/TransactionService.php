<?php

namespace AppBundle\Service\Blockchain;

use AppBundle\Entity\Blockchain\Wallet;
use AppBundle\Model\Blockchain\InputOutputDto;
use AppBundle\Model\Blockchain\Transaction;
use AppBundle\Service\Security\SecurityService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

class TransactionService
{
    private $systemSenderAddress = '0';
    private $systemPreviousTransactionHash = '0';
    private $systemPreviousBlockIndex = 0;

    /** @var  SerializerInterface */
    private $serializer;

    /** @var SecurityService */
    private $securityService;

    /**
     * TransactionService constructor.
     * @param SerializerInterface $serializer
     * @param SecurityService     $securityService
     */
    public function __construct(
        SerializerInterface $serializer,
        SecurityService $securityService
    ) {
        $this->serializer = $serializer;
        $this->securityService = $securityService;
    }

    /**
     * @param Transaction $transactions
     * @return string
     */
    public function serializeTransaction(Transaction $transactions) : string
    {
        return $this->serializer->serialize(
            $transactions,
            'json',
            SerializationContext::create()->setGroups(['Default', 'save'])
        );
    }

    /**
     * @param Transaction $transactions
     * @return string
     */
    public function serializeTransactionForHash(Transaction $transactions) : string
    {
        return $this->serializer->serialize(
            $transactions,
            'json',
            SerializationContext::create()->setGroups(['Default'])
        );
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function deserializeTransaction($data)
    {
        return $this->serializer->deserialize($data, Transaction::class, 'json');
    }

    /**
     * Genesis emission, miner reward
     * @param string $recipientAddress
     * @param float  $amount
     * @return Transaction
     */
    public function createSystemTransaction(string $recipientAddress, float $amount) : Transaction
    {
        $transaction = new Transaction(
            $this->systemSenderAddress,
            $this->systemPreviousTransactionHash,
            $this->systemPreviousBlockIndex,
            $recipientAddress,
            $amount
        );

        $this->addSystemTransactionHashAndSignature($transaction);

        return $transaction;
    }

    /**
     * @param Wallet         $senderWallet
     * @param string         $recipientAddress
     * @param InputOutputDto $availableInput
     * @return Transaction
     */
    public function createRegularTransaction(
        Wallet $senderWallet,
        string $recipientAddress,
        InputOutputDto $availableInput
    ): Transaction {
        $senderAddress = $senderWallet->getAddress();

        $transaction = new Transaction(
            $senderAddress,
            $availableInput->getTransactionHash(),
            $availableInput->getBlockIndex(),
            $recipientAddress,
            $availableInput->getTransactionAmount()
        );

        $this->addTransactionHashAndSignature($transaction, $senderWallet->getPrivateKey());

        return $transaction;
    }

    /**
     * @param Transaction $transaction
     * @param Wallet      $senderWallet
     * @return bool
     */
    public function isTransactionValid(Transaction $transaction, Wallet $senderWallet): bool
    {
        $hash = $transaction->getHash();
        if ($hash !== $this->calculateTransactionHash($transaction)) {
            return false;
        }

        $signature = $transaction->getSenderSignature();

        $signature = base64_decode($signature);

        $decryptedHash =
            $this->securityService->decryptByPublicKey($signature, $senderWallet->getPublicKey());

        /** @noinspection IfReturnReturnSimplificationInspection */
        if ($decryptedHash !== $hash) {
            return false;
        }

        return true;
    }

    /**
     * Very simple miner transaction checker
     * @param Transaction $transaction
     * @return bool
     */
    public function isMinerTransaction(Transaction $transaction): bool
    {
        if ($transaction->getSenderAddress() !== $this->systemSenderAddress) {
            return false;
        }

        if ($transaction->getPreviousTransactionHash() !== $this->systemPreviousTransactionHash) {
            return false;
        }

        if ((int) $transaction->getPreviousBlockIndex() !== $this->systemPreviousBlockIndex) {
            return false;
        }

        return true;
    }

    /**
     * @param Transaction $transaction
     * @param string      $senderPrivateKey
     */
    private function addTransactionHashAndSignature(Transaction $transaction, string $senderPrivateKey): void
    {
        $hash = $this->calculateTransactionHash($transaction);
        $signature = $this->securityService->encryptByPrivateKey($hash, $senderPrivateKey);
        $transaction->addValidationData($hash, $signature);
    }

    /**
     * @param Transaction $transaction
     * @return void
     */
    private function addSystemTransactionHashAndSignature(Transaction $transaction) : void
    {
        $hash = $this->calculateTransactionHash($transaction);
        $signature = '';

        $transaction->addValidationData($hash, $signature);
    }

    /**
     * @param Transaction $transaction
     * @return string
     */
    private function calculateTransactionHash(Transaction $transaction) : string
    {
        $serialized = $this->serializeTransactionForHash($transaction);

        return hash('sha256', $serialized);
    }
}
