<?php

namespace SfCod\DoctrineEncrypt\Services;

use ReflectionClass;
use SfCod\DoctrineEncrypt\Encryptors\EncryptorInterface;

/**
 * Class Encryptor
 * @package SfCod\DoctrineEncrypt\Services
 */
class Encryptor
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Encryptor constructor.
     *
     * @param $encryptName
     * @param $secretKey
     * @param $secretIv
     *
     * @throws \ReflectionException
     */
    public function __construct(string $encryptName, string $secretKey, string $secretIv)
    {
        $reflectionClass = new ReflectionClass($encryptName);
        $this->encryptor = $reflectionClass->newInstanceArgs([$secretKey, $secretIv]);
    }

    /**
     * Get encryptor instance
     *
     * @return EncryptorInterface
     */
    public function getEncryptor(): EncryptorInterface
    {
        return $this->encryptor;
    }

    /**
     * Decrypt value
     *
     * @param mixed $value
     *
     * @return string
     */
    public function decrypt($value)
    {
        return $this->encryptor->decrypt($value);
    }

    /**
     * Encrypt value
     *
     * @param mixed $value
     *
     * @return string
     */
    public function encrypt($value)
    {
        return $this->encryptor->encrypt($value);
    }
}