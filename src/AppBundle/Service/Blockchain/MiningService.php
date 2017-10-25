<?php
/**
 * Created by PhpStorm.
 * User: vladimir
 * Date: 05/11/2017
 * Time: 22:32
 */

namespace AppBundle\Service\Blockchain;

class MiningService
{
    /**
     * #refactor - this is blockchain settings
     * @var string
     */
    private $proofZeros = '00';

    /**
     * @param int    $lastProof
     * @param string $poolHash
     * @return int
     */
    public function calculateNextProof(int $lastProof, string $poolHash): int
    {
        $nextProof = 0;
        while ($this->isProofValid($lastProof, $nextProof, $poolHash) === false) {
            $nextProof++;
        }

        return $nextProof;
    }

    /**
     * @param int    $lastProof Previous Proof
     * @param int    $proof     Current Proof
     * @param string $poolHash
     * @return bool True if correct, False if not.
     */
    public function isProofValid(int $lastProof, int $proof, string $poolHash) : bool
    {
        $hash = hash('sha256', (string) ($lastProof * $proof) . $poolHash);

        /** @noinspection SubStrUsedAsStrPosInspection */

        return substr($hash, 0, mb_strlen($this->proofZeros)) === $this->proofZeros;
    }
}
