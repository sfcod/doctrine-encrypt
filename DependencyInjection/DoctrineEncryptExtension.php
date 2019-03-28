<?php

namespace SfCod\DoctrineEncryptBundle\DependencyInjection;

use Doctrine\Common\Annotations\Reader;
use SfCod\DoctrineEncryptBundle\Encryptors\Rijndael128Encryptor;
use SfCod\DoctrineEncryptBundle\Encryptors\Rijndael256Encryptor;
use SfCod\DoctrineEncryptBundle\Services\Encryptor;
use SfCod\DoctrineEncryptBundle\Subscribers\DoctrineEncryptSubscriber;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Initialization of bundle.
 *
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DoctrineEncryptExtension extends Extension
{
    /**
     * Supported encryptors list
     *
     * @var array
     */
    public static $supportedEncryptorClasses = [
        'rijndael256' => Rijndael256Encryptor::class,
        'rijndael128' => Rijndael128Encryptor::class,
    ];

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $supportedEncryptorClasses = self::$supportedEncryptorClasses;

        if (empty($config['secret_key'])) {
            if (getenv('APP_SECRET')) {
                $config['secret_key'] = getenv('APP_SECRET');
            } else {
                throw new \RuntimeException('You must provide "secret_key" for DoctrineEncryptBundle or "APP_SECRET"  in ".env" for framework');
            }
        }

        if (empty($config['encryptor_class'])) {
            if (isset($config['encryptor']) && isset($supportedEncryptorClasses[$config['encryptor']])) {
                $config['encryptor_class'] = $supportedEncryptorClasses[$config['encryptor']];
            } else {
                $config['encryptor_class'] = $supportedEncryptorClasses['rijndael256'];
            }
        }

        $subscriber = new Definition(DoctrineEncryptSubscriber::class);
        $subscriber->setArguments([
            new Reference(Reader::class),
            $config['encryptor_class'],
            $config['secret_key'],
        ]);
        $subscriber->addTag('doctrine.event_subscriber');

        $encryptor = new Definition(Encryptor::class);
        $encryptor->setArguments([
            $config['encryptor_class'],
            $config['secret_key'],
        ]);

        $container->addDefinitions([
            DoctrineEncryptSubscriber::class => $subscriber,
            Encryptor::class => $encryptor,
        ]);
    }

    /**
     * Get alias for configuration
     *
     * @return string
     */
    public function getAlias()
    {
        return 'sfcod_doctrine_encrypt';
    }
}
