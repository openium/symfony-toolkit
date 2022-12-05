<?php

namespace Openium\SymfonyToolKitBundle\Tests\DependencyInjection;

use Openium\SymfonyToolKitBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Class ConfigurationTest
 *
 * @package SOW\TranslationBundle\Tests\DependencyInjection
 */
class ConfigurationTest extends TestCase
{
    public function testConfiguration(): void
    {
        $configuration = new Configuration();
        $tree = $configuration->getConfigTreeBuilder();
        self::assertTrue($tree instanceof TreeBuilder);
    }
}
