<?php

namespace RonteLtd\PushBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RonteLtdPushExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('ronte_ltd_push.push_env', $mergedConfig['push_env']);
        $container->setParameter('ronte_ltd_push.apns_certificates_dir', $mergedConfig['apns_certificates_dir']);
        $container->setParameter('ronte_ltd_push.gearman_server', $mergedConfig['gearman_server']);
        $container->setParameter('ronte_ltd_push.gearman_port', $mergedConfig['gearman_port']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
