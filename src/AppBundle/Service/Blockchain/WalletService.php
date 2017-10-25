<?php

namespace AppBundle\Service\Blockchain;

use AppBundle\Entity\Blockchain\Wallet;
use AppBundle\Repository\Blockchain\WalletRepository;
use AppBundle\Service\Blockchain;

class WalletService
{
    private const LOGIN_CREATOR = 'creator';
    private const LOGIN_ALICE = 'alice';
    private const LOGIN_BOB = 'bob';

    /**
     * @var WalletRepository
     */
    private $repository;

    /**
     * @var Blockchain
     */
    private $blockchain;

    /**
     * WalletService constructor.
     * @param WalletRepository $repository
     * @param Blockchain       $blockchain
     */
    public function __construct(
        WalletRepository $repository,
        Blockchain $blockchain
    ) {
        $this->repository = $repository;
        $this->blockchain = $blockchain;
    }

    /**
     * @return array
     */
    public function getLoginToAvailableInputs() : array
    {
        $wallets = $this->getAllWallets();

        $result = [];
        foreach ($wallets as $wallet) {
            // #refactoing - fetch all wallets balance at once not one by one
            $result[$wallet->getLogin()] = $this->blockchain->getAvailableInputs($wallet->getAddress());
        }

        return $result;
    }

    /**
     * @return Wallet
     */
    public function getCreatorWallet() : Wallet
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->repository->findOneBy(['login' => self::LOGIN_CREATOR]);
    }

    /**
     * @return Wallet
     */
    public function getAliceWallet() : Wallet
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->repository->findOneBy(['login' => self::LOGIN_ALICE]);
    }

    /**
     * @return Wallet
     */
    public function getBobWallet() : Wallet
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->repository->findOneBy(['login' => self::LOGIN_BOB]);
    }

    /**
     * @return Wallet[]
     */
    public function getAllWallets() : array
    {
        return $this->repository->findAll();
    }

    /**
     * @return Wallet[]
     */
    public function getWalletsIndexedByLogin() : array
    {
        $data = $this->getAllWallets();

        $result = [];
        foreach ($data as $item) {
            $result[$item->getLogin()] = $item;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getWalletsIndexedByAddress() : array
    {
        $data = $this->getAllWallets();

        $result = [];
        foreach ($data as $item) {
            $result[$item->getAddress()] = $item;
        }

        return $result;
    }
}
