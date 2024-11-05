<?php

namespace Mikamatto\BreadcrumbsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('breadcrumbs_bundle');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('routes')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('label')->end()
                            ->arrayNode('chain')
                                ->scalarPrototype()->end() // This allows an array of strings
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('breadcrumbs_file')
                    ->defaultValue('%kernel.project_dir%/config/packages/breadcrumbs.yaml')
                    ->info('Path to the YAML file containing breadcrumb configurations.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}