<?php

/**
 * Configuration class
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\DependencyInjection
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\DependencyInjection;

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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('openium_symfony_toolkit');
        //$rootNode = method_exists(TreeBuilder::class, 'getRootNode') ?
        // $treeBuilder->getRootNode() :
        // $treeBuilder->root('openium_symfony_toolkit');
        return $treeBuilder;
    }
}
