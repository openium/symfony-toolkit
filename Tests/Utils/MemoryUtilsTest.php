<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use Openium\SymfonyToolKitBundle\Utils\MemoryUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class MemoryUtilsTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Utils
 */
class MemoryUtilsTest extends TestCase
{
    public function testConvert()
    {
        self::assertEquals('1 kb', MemoryUtils::convert(1024));
        self::assertEquals('2 kb', MemoryUtils::convert(2048));
        self::assertEquals('4 mb', MemoryUtils::convert(4 * 1024 * 1024));
        self::assertEquals('2 gb', MemoryUtils::convert(2 * 1024 * 1024 * 1024));
    }
}
