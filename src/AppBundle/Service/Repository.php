<?php

namespace AppBundle\Service;

class Repository
{
    private $creatorAddress = 'creator';
    private $aliceAddress = 'alice';
    private $bobAddress = 'bob';

    private $basicEmission = 50;
    private $basicProof = 5;

    /**
     * @return int
     */
    public function getBasicEmission(): int
    {
        return $this->basicEmission;
    }

    /**
     * @return int
     */
    public function getBasicProof()
    {
        return $this->basicProof;
    }

    public function getBobAddressHash()
    {
        return $this->getAddressHash($this->bobAddress);
    }

    public function getAliceAddressHash()
    {
        return $this->getAddressHash($this->aliceAddress);
    }

    public function getCreatorPublicKey()
    {
        return $this->getAddressHash($this->creatorAddress);
    }

    private function getAddressHash(string $address)
    {
        return hash('sha256', $address);
    }
}
