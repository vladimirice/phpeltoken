<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 05/11/2017
 * Time: 20:21
 */

namespace AppBundle\Model\Blockchain;

class InputOutputDto
{
    /** @var int  */
    private $blockIndex;
    /** @var Transaction  */
    private $transaction;

    /**
     * InputOutputDto constructor.
     * @param int         $blockIndex
     * @param Transaction $transaction
     */
    public function __construct(int $blockIndex, Transaction $transaction)
    {
        $this->blockIndex = $blockIndex;
        $this->transaction = $transaction;
    }

    /**
     * @return int
     */
    public function getBlockIndex() : int
    {
        return $this->blockIndex;
    }

    /**
     * @return Transaction
     */
    public function getTransaction() : Transaction
    {
        return $this->transaction;
    }

    /**
     * @param int $amount
     * @return bool
     */
    public function isAvailableForAmount(int $amount): bool
    {
        return $this->getTransaction()->getAmount() === $amount;
    }

    /**
     * @return string
     */
    public function getTransactionHash(): string
    {
        return $this->getTransaction()->getHash();
    }

    /**
     * @return int
     */
    public function getTransactionAmount(): int
    {
        return $this->getTransaction()->getAmount();
    }
}
