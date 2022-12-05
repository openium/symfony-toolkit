<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use DateTime;
use Openium\SymfonyToolKitBundle\Utils\DateStringUtils;
use PHPUnit\Framework\TestCase;

class DateStringUtilsTest extends TestCase
{
    public function testGetDateTimeFromStringFullDate(): void
    {
        $result = DateStringUtils::getDateTimeFromString("2021-06-12T14:41:26+02:00");
        self::assertInstanceOf(DateTime::class, $result);
    }

    public function testGetDateTimeFromStringShortDate(): void
    {
        $result = DateStringUtils::getDateTimeFromString("2021-06-12");
        self::assertInstanceOf(DateTime::class, $result);
    }

    public function testGetDateTimeFromStringWrongDate(): void
    {
        $result = DateStringUtils::getDateTimeFromString("2021-12");
        self::assertFalse($result);
    }
}
