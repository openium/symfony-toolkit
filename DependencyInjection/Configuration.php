<?php

namespace Openium\SymfonyToolKitBundle\DependencyInjection;

use Openium\SymfonyToolKitBundle\EventListener\PathKernelExceptionListener;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Openium\SymfonyToolKitBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('openium_symfony_toolkit');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('uploads')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('public_dir')
                            ->defaultValue('%kernel.project_dir%/public')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('dir_name')
                            ->defaultValue('uploads')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('kernel_exception_listener')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('path')
                            ->defaultValue('/api')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('class')
                            ->defaultValue(PathKernelExceptionListener::class)
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
