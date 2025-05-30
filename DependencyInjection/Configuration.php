<?php

declare(strict_types=1);

namespace Patt\SuluConfigurationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sulu_configuration');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->arrayNode('configurations')
                ->children()
                    ->arrayNode('directories')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}