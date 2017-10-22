<?php

namespace AppBundle\Faker\Provider;

use Faker\Provider\Base as BaseProvider;

class WalletProvider extends BaseProvider
{

    private $config = array(
        'digest_alg'        => 'sha512',
        'private_key_bits'  => 4096,
        'private_key_type'  => OPENSSL_KEYTYPE_RSA,
    );

    public function walletInit(string $login)
    {
        $pair = $this->generateNewKeyPair();

        return [
            'privateKey' => $pair['privateKey'],
            'publicKey' => $pair['publicKey'],
            'address' => md5($login),
            'login' => $login,
        ];
    }

    /**
     * @return array
     */
    public function generateNewKeyPair() : array
    {
        // Create the private and public key
        $pair = openssl_pkey_new($this->config);
        openssl_pkey_export($pair, $privateKey);

        $publicKey = openssl_pkey_get_details($pair)['key'];

        return [
            'privateKey'    => $privateKey,
            'publicKey'     => $publicKey,
        ];
    }

    private $charlieAssymetricPair = [
        'privateKey' => '-----BEGIN PRIVATE KEY-----
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
u78bkJNVSZo53p3ue5jMApznup3cWg==
-----END PRIVATE KEY-----
',
        'publicKey' => '-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEA2ubtj8BBcxZWr1y9ptDl
uvAwxxv39Y0U5WKHSE2wF71ciqWziiUmv1EQEzwopvFKZYtshfq7z5AlTuNsU6Ub
ckiymon+wra1rr9Z23MvYfqrNPu2jdkUlb/olRFTwqp1wudRUqpFzHmJYRxmjUGf
68wHlEYzpA3HJCXi/rhC3IGm6gw89Gmyh+A4BzokEEAQSMNeiBB2HSxfQWVfUaJS
ha7nBc22tn+RJ6OkQhKHcncdAgavtAaMoCCecgPhBFd5+52x9AjFshWvRkAbnD3m
Lrrc4q74HIRiTCjJw4X2quKKDva/dOsWxbGXU+EVQAhYA4QRbul3yVGMbp7S269O
7flXR2QYwLzzrMZtlS3MM/8awvLrDV/SwJoj1fAraJkvLKFNWYJeD4EPfMZTSlDm
I/oXbZob8fH0nC8/HAgKz9NqCfcC1T849gwyC6M2P8lTsL89RMDiU/DRQm/y1bbt
lrhUwxwCpeYWluxeJgfBR9vUeeczUiN0D5ZPNfTIG9UDZ100E4LWN6O94ycPlhue
U1zUolGVT1NpmzwD4hP6QoCHLrcZPKnp6bnaCC/JOe8IEGL4lCjuNnupG6SkrqYn
l4T9bkpkK3U5Qqz0G3iWZmD6nZoSq23N3rzeUcCvOxnNUTS6g0NSKrupQUDIilGc
1qiwa6UiBlYrTy0qs0BIp8MCAwEAAQ==
-----END PUBLIC KEY-----
',
        ];

}