<?php

namespace AppBundle\Model;

class Transaction
{
    /**
     * @var string
     */
    private $senderAddress;
    /**
     * @var string
     */
    private $recipientAddress;
    /**
     * @var int
     */
    private $amount;

    /**
     * Transaction constructor.
     * @param string $senderAddress
     * @param string $recipientAddress
     * @param int    $amount
     */
    public function __construct(
        string $senderAddress,
        string $recipientAddress,
        int $amount
    ) {
        $this->senderAddress    = $senderAddress;
        $this->recipientAddress = $recipientAddress;
        $this->amount           = $amount;
    }

    /**
     * @return array
     */
    public function getAsArray() : array
    {
        return [
            'senderAddress' => $this->senderAddress,
            'recipient'     => $this->recipientAddress,
            'amount'        => $this->amount,
        ];
    }

    /**
     * @return string
     */
    public function getChecksum(): string
    {
        return md5(json_encode($this->getAsArray()));
    }
}
