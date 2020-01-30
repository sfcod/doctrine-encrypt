<?php

namespace SfCod\DoctrineEncrypt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Configuration tree for security bundle. Full tree you can see in Resources/docs
 * 
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class DoctrineEncryptConfiguration implements ConfigurationInterface {

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder() {

        if (Kernel::VERSION_ID >= 40300) {
            $treeBuilder = new TreeBuilder('sfcod_doctrine_encrypt');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sfcod_doctrine_encrypt');
        }

        $rootNode
                ->children()
                    ->scalarNode('secret_key')
                    ->end()
                    ->scalarNode('secret_iv')
                    ->end()
                    ->scalarNode('encryptor')
                    ->end()
                    ->scalarNode('encryptor_class')
                    ->end()
                ->end();

        return $treeBuilder;
    }

}