<?php

namespace AppBundle\Entity\Blockchain;

use AppBundle\Entity\Traits\IdTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Blockchain\WalletRepository")
 */
class Wallet
{
    use IdTrait;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $privateKey;
    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $publicKey;
    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $address;
    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $login;

    /**
     * @param array $data
     */
    public function init(array $data): void
    {
        $this
            ->setPublicKey($data['publicKey'])
            ->setPrivateKey($data['privateKey'])
            ->setAddress($data['address'])
            ->setLogin($data['login'])
        ;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return $this
     */
    public function setPrivateKey(string $privateKey)
    {
        $this->privateKey = $privateKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return $this
     */
    public function setPublicKey(string $publicKey)
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(string $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login)
    {
        $this->login = $login;

        return $this;
    }
}
