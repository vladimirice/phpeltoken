<?php

namespace AppBundle\Model\Blockchain;

use JMS\Serializer\Annotation as JMSAnnotation;

class Block
{
    /**
     * @var int
     * @JMSAnnotation\Type("int")
     */
    private $index;
    /**
     * @var int
     * @JMSAnnotation\Type("int")
     */
    private $timestamp;
    /**
     * @var array
     * @JMSAnnotation\Type("array<AppBundle\Model\Blockchain\Transaction>")
     */
    private $transactions;
    /**
     * @var int
     * @JMSAnnotation\Type("int")
     */
    private $proof;
    /**
     * @var string
     * @JMSAnnotation\Type("string")
     */
    private $previousHash;
    /**
     * @var string
     * @JMSAnnotation\Type("string")
     * @JMSAnnotation\Groups({"save"})
     */
    private $hash;

    /**
     * Block constructor.
     * @param int    $index
     * @param int    $proof
     * @param string $previousHash
     * @param array  $transactions
     */
    public function __construct(
        int $index,
        int $proof,
        string $previousHash,
        array $transactions
    ) {
        $this->index = $index;
        $this->timestamp = time();
        $this->transactions = $transactions;
        $this->proof = $proof;
        $this->previousHash = $previousHash;
    }

    /**
     * @return int
     */
    public function getIndex() : int
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return int
     */
    public function getProof(): int
    {
        return $this->proof;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getPreviousHash(): string
    {
        return $this->previousHash;
    }
}
