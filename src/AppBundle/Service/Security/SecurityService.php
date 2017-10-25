<?php

namespace AppBundle\Service\Security;

class SecurityService
{
    private $config = [
        'digest_alg'        => 'sha512',
        'private_key_bits'  => 4096,
        'private_key_type'  => OPENSSL_KEYTYPE_RSA,
    ];

    /**
     * @return array
     */
    public function generateNewKeyPair() : array
    {
        // Create the private and public key
        $pair = openssl_pkey_new($this->config);
        openssl_pkey_export($pair, $privateKey);

        $publicKey = openssl_pkey_get_details($pair)['key'];

        return compact('privateKey', 'publicKey');
    }

    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public function encryptByPrivateKey(string $data, string $key) : string
    {
        openssl_private_encrypt($data, $result, $key);

        return $result;
    }

    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public function decryptByPublicKey(string $data, string $key) : ?string
    {
        openssl_public_decrypt($data, $result, $key);

        return $result;
    }
}
