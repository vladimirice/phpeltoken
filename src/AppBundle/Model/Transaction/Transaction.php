<?php

namespace AppBundle\Model\Transaction;

class Transaction
{
    /**
     * Transaction checksum
     * @var string
     */
    private $hash;
    /**
     * Signature to validate sender
     * @var string
     */
    private $senderSignature;
    /**
     * Sender public key to validate transaction
     * @var string
     */
    private $senderPublicKey;
    /**
     * Previous transaction hash. The source of the funds
     * @var string
     */
    private $previousTransactionHash;
    /**
     * Block hash where previous transaction is stored
     * @var string
     */
    private $previousBlockHash;
    /**
     * A Hash of recipient address
     * @var string
     */
    private $recipientPublicKey;
    /**
     * @var float
     */
    private $amount;

    /**
     * Transaction constructor.
     * @param string $senderPublicKey
     * @param string $previousTransactionHash
     * @param string $previousBlockHash
     * @param string $recipientPublicKey
     * @param float  $amount
     */
    public function __construct(
        string $senderPublicKey,
        string $previousTransactionHash,
        string $previousBlockHash,
        string $recipientPublicKey,
        float $amount
    ) {
        $this->senderPublicKey = $senderPublicKey;
        $this->previousTransactionHash = $previousTransactionHash;
        $this->previousBlockHash = $previousBlockHash;
        $this->recipientPublicKey = $recipientPublicKey;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getSenderSignature(): string
    {
        return $this->senderSignature;
    }

    /**
     * @return string
     */
    public function getSenderPublicKey(): string
    {
        return $this->senderPublicKey;
    }

    /**
     * @return string
     */
    public function getPreviousTransactionHash(): string
    {
        return $this->previousTransactionHash;
    }

    /**
     * @return string
     */
    public function getPreviousBlockHash(): string
    {
        return $this->previousBlockHash;
    }

    /**
     * @return string
     */
    public function getRecipientPublicKey(): string
    {
        return $this->recipientPublicKey;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @param string $hash
     * @param string $senderSignature
     * @return $this
     */
    public function addTransactionValidations(string $hash, string $senderSignature)
    {
        $this->senderSignature = $senderSignature;
        $this->hash = $hash;

        return $this;
    }
}
