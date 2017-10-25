<?php

namespace AppBundle\Model\Blockchain;

use JMS\Serializer\Annotation as JMSAnnotation;

class Transaction
{
    /**
     * Transaction checksum
     * @var string
     * @JMSAnnotation\Type("string")
     * @JMSAnnotation\Groups({"save"})
     */
    private $hash;
    /**
     * Signature to validate sender
     * @var string
     * @JMSAnnotation\Type("string")
     * @JMSAnnotation\Groups({"save"})
     */
    private $senderSignature;
    /**
     * Sender public key to validate transaction
     * @var string
     * @JMSAnnotation\Type("string")
     */
    private $senderAddress;
    /**
     * Previous transaction hash. The source of the funds
     * @var string
     * @JMSAnnotation\Type("string")
     */
    private $previousTransactionHash;
    /**
     * Block hash where previous transaction is stored
     * @var int
     * @JMSAnnotation\Type("int")
     */
    private $previousBlockIndex;
    /**
     * A Hash of recipient address
     * @var string
     * @JMSAnnotation\Type("string")
     */
    private $recipientAddress;
    /**
     * @var int
     * @JMSAnnotation\Type("int")
     */
    private $amount;

    /**
     * Transaction constructor.
     * @param string $senderAddress
     * @param string $previousTransactionHash
     * @param int    $previousBlockIndex
     * @param string $recipientAddress
     * @param int    $amount
     */
    public function __construct(
        string $senderAddress,
        string $previousTransactionHash,
        int $previousBlockIndex,
        string $recipientAddress,
        int $amount
    ) {
        $this->senderAddress = $senderAddress;
        $this->previousTransactionHash = $previousTransactionHash;
        $this->previousBlockIndex = $previousBlockIndex;
        $this->recipientAddress = $recipientAddress;
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
    public function getSenderAddress(): string
    {
        return $this->senderAddress;
    }

    /**
     * @param string $address
     * @return bool
     */
    public function isSender(string $address): bool
    {
        return $this->getSenderAddress() === $address;
    }

    /**
     * @param string $address
     * @return bool
     */
    public function isRecipient(string $address): bool
    {
        return $this->getRecipientAddress() === $address;
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
    public function getPreviousBlockIndex(): string
    {
        return $this->previousBlockIndex;
    }

    /**
     * @return string
     */
    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
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
    public function addValidationData(string $hash, string $senderSignature)
    {
        $this->senderSignature = base64_encode($senderSignature);
        $this->hash = $hash;

        return $this;
    }
}
