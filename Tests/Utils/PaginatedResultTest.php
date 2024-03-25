<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use Openium\SymfonyToolKitBundle\Utils\FilterParameters;
use Openium\SymfonyToolKitBundle\Utils\PaginatedResult;
use PHPUnit\Framework\TestCase;

class PaginatedResultTest extends TestCase
{
    public function testPaginatedResult()
    {
        $fp1 = new FilterParameters(
            'search',
            2,
            20,
            'ASC',
            'email'
        );
        $pr1 = new PaginatedResult([], $fp1, 145);
        self::assertEquals(8, $pr1->getTotalPage());
        $fp2 = new FilterParameters('search');
        $pr2 = new PaginatedResult([], $fp2, 55);
        self::assertEquals(1, $pr2->getTotalPage());
    }
}
