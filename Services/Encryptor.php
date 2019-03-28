<?php

namespace SfCod\DoctrineEncryptBundle\Services;

use ReflectionClass;
use SfCod\DoctrineEncryptBundle\Encryptors\EncryptorInterface;

/**
 * Class Encryptor
 * @package SfCod\DoctrineEncryptBundle\Services
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
     * @param $key
     *
     * @throws \ReflectionException
     */
    public function __construct(string $encryptName, string $key)
    {
        $reflectionClass = new ReflectionClass($encryptName);
        $this->encryptor = $reflectionClass->newInstanceArgs([$key]);
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