<?php

namespace SfCod\DoctrineEncrypt;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use SfCod\DoctrineEncrypt\DependencyInjection\DoctrineEncryptExtension;
use SfCod\DoctrineEncrypt\DependencyInjection\Compiler\RegisterServiceCompilerPass;

class DoctrineEncryptBundle extends Bundle {
    
    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass(new RegisterServiceCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
    
    public function getContainerExtension()
    {
        return new DoctrineEncryptExtension();
    }
}
