<?php

namespace SfCod\DoctrineEncrypt\DependencyInjection;

use Doctrine\Common\Annotations\Reader;
use SfCod\DoctrineEncrypt\Command\DoctrineDecryptDatabaseCommand;
use SfCod\DoctrineEncrypt\Command\DoctrineEncryptDatabaseCommand;
use SfCod\DoctrineEncrypt\Command\DoctrineEncryptStatusCommand;
use SfCod\DoctrineEncrypt\Encryptors\Rijndael128Encryptor;
use SfCod\DoctrineEncrypt\Encryptors\AES256Encryptor;
use SfCod\DoctrineEncrypt\Services\Encryptor;
use SfCod\DoctrineEncrypt\Subscribers\DoctrineEncryptSubscriber;
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
        AES256Encryptor::class,
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

        if (empty($config['secret_iv'])) {
            if (getenv('APP_SECRET')) {
                $config['secret_iv'] = strrev(getenv('APP_SECRET'));
            } else {
                throw new \RuntimeException('You must provide "secret_iv" for DoctrineEncryptBundle or "APP_SECRET"  in ".env" for framework');
            }
        }

        if (empty($config['encryptor_class'])) {
            if (isset($config['encryptor']) && in_array($config['encryptor'], $supportedEncryptorClasses)) {
                $config['encryptor_class'] = $config['encryptor'];
            } else {
                $config['encryptor_class'] = $supportedEncryptorClasses[0];
            }
        }

        $subscriber = new Definition(DoctrineEncryptSubscriber::class);
        $subscriber->setArguments([
            new Reference(Reader::class),
            $config['encryptor_class'],
            $config['secret_key'],
            $config['secret_iv'],
        ]);
        $subscriber->addTag('doctrine.event_subscriber');

        $encryptor = new Definition(Encryptor::class);
        $encryptor->setArguments([
            $config['encryptor_class'],
            $config['secret_key'],
            $config['secret_iv'],
        ]);

        $container->addDefinitions([
            DoctrineEncryptSubscriber::class => $subscriber,
            Encryptor::class => $encryptor,
        ]);

        $this->registerCommands($configs, $container);
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

    /**
     * Register encryption commands
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function registerCommands(array $config, ContainerBuilder $container)
    {
        $encryptStatus = new Definition(DoctrineEncryptStatusCommand::class);
        $encryptStatus
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('console.command');

        $encryptDatabase = new Definition(DoctrineEncryptDatabaseCommand::class);
        $encryptDatabase
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('console.command');

        $decryptDatabase = new Definition(DoctrineDecryptDatabaseCommand::class);
        $decryptDatabase
            ->setAutoconfigured(true)
            ->setAutowired(true)
            ->addTag('console.command');

        $container->addDefinitions([
            DoctrineEncryptStatusCommand::class => $encryptStatus,
            DoctrineEncryptDatabaseCommand::class => $encryptDatabase,
            DoctrineDecryptDatabaseCommand::class => $decryptDatabase,
        ]);
    }
}
