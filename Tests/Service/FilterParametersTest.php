<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Utils\FilterParameters;
use PHPUnit\Framework\TestCase;

class FilterParametersTest extends TestCase
{
    public function testFilterParameters(): void
    {
        $fp = new FilterParameters(
            'search',
            2,
            20,
            'ASC',
            'email'
        );
        self::assertEquals(20, $fp->getOffset());
        self::assertEquals('7474352c69ebd9e716b8a848482b852d5bae2710', $fp->getHash());
        $fp2 = new FilterParameters(
            'search',
            5,
            10,
            'ASC',
            'email'
        );
        self::assertEquals(40, $fp2->getOffset());
        self::assertNotEquals($fp->getHash(), $fp2->getHash());
    }
}
