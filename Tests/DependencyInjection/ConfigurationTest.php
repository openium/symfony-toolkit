<?php

namespace Openium\SymfonyToolKitBundle\Tests\DependencyInjection;

use Openium\SymfonyToolKitBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testConfiguration(): void
    {
        $configuration = new Configuration();
        $treeBuilder = $configuration->getConfigTreeBuilder();
        self::assertTrue($treeBuilder instanceof TreeBuilder);
    }
}
