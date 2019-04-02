<?php

namespace SfCod\DoctrineEncrypt\Encryptors;

/**
 * Class for variable encryption
 *
 * @author Marcel van Nuil <marcel@ambta.com>
 */
class AES256Encryptor implements EncryptorInterface
{
    /**
     * Encryption method
     */
    private const ENCRYPT_METHOD = 'AES-256-CBC';

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var string
     */
    private $secretIv;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $secretKey, string $secretIv)
    {
        $this->secretKey = hash('sha256', $secretKey);
        $this->secretIv = substr(hash('sha256', $secretIv), 0, openssl_cipher_iv_length(self::ENCRYPT_METHOD));
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        if (is_string($data)) {
            return trim(base64_encode(openssl_encrypt(
                    $data,
                    self::ENCRYPT_METHOD,
                    $this->secretKey,
                    0,
                    $this->secretIv
                ))) . "<ENC>";
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($data)
    {
        if (is_string($data)) {
            $data = str_replace("<ENC>", "", $data);

            return trim(openssl_decrypt(
                base64_decode($data),
                self::ENCRYPT_METHOD,
                $this->secretKey,
                0,
                $this->secretIv
            ));
        }

        return $data;
    }
}
