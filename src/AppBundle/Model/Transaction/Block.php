<?php

namespace AppBundle\Model\Transaction;

class Block
{
    /**
     * @var int
     */
    private $index;
    /**
     * @var int
     */
    private $timestamp;
    /**
     * @var array
     */
    private $transactions;
    /**
     * @var int
     */
    private $proof;
    /**
     * @var string
     */
    private $previousHash;
    /**
     * @var string
     */
    private $hash;

    /**
     * Block constructor.
     * @param int    $index
     * @param array  $transactions
     * @param int    $proof
     * @param string $previousHash
     */
    public function __construct(
        int $index,
        array $transactions,
        int $proof,
        string $previousHash
    ) {
        $this->index = $index;
        $this->timestamp = time();
        $this->transactions = $transactions;
        $this->proof = $proof;
        $this->previousHash = $previousHash;

        $this->generateHash();
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
     * @return int
     */
    public function getProof(): int
    {
        return $this->proof;
    }

    /**
     * @return array
     */
    public function getAsArray() : array
    {
        return [
            'index'         => $this->index,
            'timestamp'     => $this->timestamp,
            'transactions'  => $this->transactions,
            'proof'         => $this->proof,
            'previousHash'  => $this->previousHash,
            'hash'          => $this->hash,
        ];
    }

    /**
     * @return void
     */
    private function generateHash(): void
    {
        $asArray = $this->getAsArray();

        $this->hash = md5(json_encode($asArray));
    }
}
