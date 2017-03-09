<?php

namespace RonteLtd\PushBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ronte_push');

        $rootNode
            ->children()
                ->scalarNode('push_env')->end()
                ->scalarNode('apns_certificates_dir')->end()
                ->scalarNode('gearman_server')->end()
                ->scalarNode('gearman_port')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
