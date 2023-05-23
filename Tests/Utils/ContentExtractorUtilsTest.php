<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use DateTimeImmutable;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorArrayPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorBooleanPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorDateFormatException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorFloatPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorIntegerPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorMissingParameterException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorStringPropertyException;
use Openium\SymfonyToolKitBundle\Utils\ContentExtractorUtils;
use PHPUnit\Framework\TestCase;

/**
 * Class ContentExtractorUtilsTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Utils
 */
class ContentExtractorUtilsTest extends TestCase
{
    // checkKeyExists
    public function testCheckKeyExistsWithoutKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyExists($content, $key, $nullable);
    }

    public function testCheckKeyExistsWithNullKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyExists($content, $key, $nullable);
    }

    public function testCheckKeyExistsWithNullKeyAndNullable()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyExists($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $e) {
            self::fail('ContentExtractorMissingParameterException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyExistsWithEmptyStringKey()
    {
        // given
        $content = ['key' => ''];
        $key = 'key';
        $nullable = false;
        // when
        try {
            ContentExtractorUtils::checkKeyExists($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $e) {
            self::fail('ContentExtractorMissingParameterException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyExistsWithStringKey()
    {
        // given
        $content = ['key' => 'value'];
        $key = 'key';
        $nullable = false;
        // when
        try {
            ContentExtractorUtils::checkKeyExists($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $e) {
            self::fail('ContentExtractorMissingParameterException');
        }
        // then
        self::assertTrue(true);
    }

    // checkKeyIsString
    public function testCheckKeyIsStringWithoutKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsString($content, $key);
    }

    public function testCheckKeyIsStringWithNullValue()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsString($content, $key, $nullable);
    }

    public function testCheckKeyIsStringWithNullValueAllowNullable()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsString($content, $key, $nullable);
        } catch (ContentExtractorStringPropertyException $e) {
            self::fail('ContentExtractorStringPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsStringWithString()
    {
        // given
        $content = ['key' => 'value'];
        $key = 'key';
        $nullable = false;
        // when
        try {
            ContentExtractorUtils::checkKeyIsString($content, $key, $nullable);
        } catch (ContentExtractorStringPropertyException $e) {
            self::fail('ContentExtractorStringPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsStringWithIntValue()
    {
        self::expectException(ContentExtractorStringPropertyException::class);
        // given
        $content = ['key' => 3];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsString($content, $key, $nullable);
    }

    // checkKeyIsBoolean
    public function testCheckKeyIsBooleanWithoutKey(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    // checkKeyIsBoolean
    public function testCheckKeyIsBooleanWithEmptyStringKey(): void
    {
        self::expectException(ContentExtractorBooleanPropertyException::class);
        // given
        $content = ['key' => ''];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    public function testCheckKeyIsBooleanWithNumberKey(): void
    {
        self::expectException(ContentExtractorBooleanPropertyException::class);
        // given
        $content = ['key' => 5];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    public function testCheckKeyIsBooleanWithBoolKey(): void
    {
        // given
        $content = ['key' => true];
        $key = 'key';
        // when
        try {
            ContentExtractorUtils::checkKeyIsBoolean($content, $key);
        } catch (ContentExtractorBooleanPropertyException) {
            self::fail('ContentExtractorBooleanPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // checkKeyIsInt
    public function testCheckKeyIsIntWithoutKey(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
    }

    public function testCheckKeyIsIntWithNullKey(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
    }

    public function testCheckKeyIsIntWithNullKeyAndNullbale(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
        } catch (ContentExtractorIntegerPropertyException) {
            self::fail('ContentExtractorIntegerPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsIntWithInt(): void
    {
        // given
        $content = ['key' => 5];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
        } catch (ContentExtractorIntegerPropertyException) {
            self::fail('ContentExtractorIntegerPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsIntWithFloat(): void
    {
        self::expectException(ContentExtractorIntegerPropertyException::class);
        // given
        $content = ['key' => 5.5];
        $key = 'key';
        $nullable = true;
        // when
        ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
    }

    // checkKeyIsFloat
    public function testCheckKeyIsFloatWithoutKey(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithNullKey(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithNullKeyAndNullbale(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorFloatPropertyException) {
            self::fail('ContentExtractorFloatPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsFloatWithInt(): void
    {
        self::expectException(ContentExtractorFloatPropertyException::class);
        // given
        $content = ['key' => 5];
        $key = 'key';
        $nullable = true;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithFloat(): void
    {
        // given
        $content = ['key' => 5.5];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorFloatPropertyException) {
            self::fail('ContentExtractorFloatPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // checkKeyIsArray
    public function testcheckKeyIsArrayWithString(): void
    {
        self::expectException(ContentExtractorArrayPropertyException::class);
        // given
        $content = ['key' => 'not an array'];
        $key = 'key';
        $allowEmpty = true;
        // when
        ContentExtractorUtils::checkKeyIsArray($content, $key, $allowEmpty);
    }

    public function testcheckKeyIsArrayWithArray(): void
    {
        // given
        $content = ['key' => ['an array']];
        $key = 'key';
        $allowEmpty = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsArray($content, $key, $allowEmpty);
        } catch (ContentExtractorArrayPropertyException) {
            self::fail('ContentExtractorArrayPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // getString
    public function testGetStringWithRequiredNotNullString(): void
    {
        // given
        $content = ['key' => '5'];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getString($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals('5', $result);
    }

    public function testGetStringWithMissingButRequiredNotNullString(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getString($content, $key, $required, $default, $nullable);
    }

    public function testGetStringWithNullButRequiredNotNullString(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getString($content, $key, $required, $default, $nullable);
    }

    public function testGetStringWithNullRequiredString(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = true;
        // when
        $result = ContentExtractorUtils::getString($content, $key, $required, $default, $nullable);
        // then
        self::assertNull($result);
    }

    public function testGetStringWithMissingAndDefaultString(): void
    {
        // given
        $content = [];
        $key = 'key';
        $required = false;
        $default = 'default';
        $nullable = true;
        // when
        $result = ContentExtractorUtils::getString($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals($default, $result);
    }

    // getBool
    public function testGetBoolWithRequiredNotNull(): void
    {
        // given
        $content = ['key' => true];
        $key = 'key';
        $required = true;
        $default = null;
        // when
        $result = ContentExtractorUtils::getBool($content, $key, $required, $default);
        // then
        self::assertTrue($result);
    }

    public function testGetBoolNullWithNutRequiredAndDefault(): void
    {
        // given
        $content = [];
        $key = 'key';
        $required = false;
        $default = true;
        // when
        $result = ContentExtractorUtils::getBool($content, $key, $required, $default);
        // then
        self::assertTrue($result);
    }

    // getInt
    public function testGetIntWithRequiredNotNullInt(): void
    {
        // given
        $content = ['key' => 5];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getInt($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals(5, $result);
    }

    public function testGetIntWithStringRequiredNotNullInt(): void
    {
        self::expectException(ContentExtractorIntegerPropertyException::class);
        // given
        $content = ['key' => '5'];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getInt($content, $key, $required, $default, $nullable);
    }

    public function testGetIntWithMissingDefault(): void
    {
        // given
        $content = [];
        $key = 'key';
        $required = false;
        $default = 5;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getInt($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals(5, $result);
    }

    public function testGetIntWithNull(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = false;
        $default = null;
        $nullable = true;
        // when
        $result = ContentExtractorUtils::getInt($content, $key, $required, $default, $nullable);
        // then
        self::assertNull($result);
    }

    // getFloat
    public function testGetFloatWithRequiredNotNullInt(): void
    {
        // given
        $content = ['key' => 5.5];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getFloat($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals(5.5, $result);
    }

    public function testGetFloatWithStringRequiredNotNullInt(): void
    {
        self::expectException(ContentExtractorFloatPropertyException::class);
        // given
        $content = ['key' => '5'];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getFloat($content, $key, $required, $default, $nullable);
    }

    public function testGetFloatWithMissingDefault(): void
    {
        // given
        $content = [];
        $key = 'key';
        $required = false;
        $default = 5.5;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getFloat($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals(5.5, $result);
    }

    public function testGetFloatWithNull(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = false;
        $default = null;
        $nullable = true;
        // when
        $result = ContentExtractorUtils::getFloat($content, $key, $required, $default, $nullable);
        // then
        self::assertNull($result);
    }

    // getDateTimeInterface
    public function testGetDateTimeInterfaceWithStringRequiredNotNull(): void
    {
        // given
        $content = ['key' => "2021-06-12T14:41:26+02:00"];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
        // then
        self::assertInstanceOf(\DateTime::class, $result);
    }

    public function testGetDateTimeInterfaceWithShortStringRequiredNotNull(): void
    {
        // given
        $content = ['key' => "2021-06-12"];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
        // then
        self::assertInstanceOf(\DateTime::class, $result);
    }

    public function testGetDateTimeInterfaceBadFormatWithStringRequiredNotNull(): void
    {
        self::expectException(ContentExtractorDateFormatException::class);
        // given
        $content = ['key' => "06-12T14:41:26+02:00"];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
    }

    public function testGetDateTimeInterfaceNullWithStringRequiredNotNull(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $nullable = false;
        // when
        ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
    }

    public function testGetDateTimeInterfaceNullWithStringNotRequiredNullable(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = false;
        $default = null;
        $nullable = true;
        // when
        $result = ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
        // then
        self::assertNull($result);
    }

    public function testGetDateTimeInterfaceNullWithDefaultStringNotRequiredNotNull(): void
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = false;
        $default = new DateTimeImmutable();
        $nullable = false;
        // when
        $result = ContentExtractorUtils::getDateTimeInterface($content, $key, $required, $default, $nullable);
        // then
        self::assertEquals($default, $result);
    }

    // getArray
    public function testGetArrayWithRequiredArray(): void
    {
        // given
        $content = ['key' => ['5']];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = false;
        // when
        $result = ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
        // then
        self::assertEquals(['5'], $result);
    }

    public function testGetArrayWithRequiredArrayButEmptyArray(): void
    {
        self::expectException(ContentExtractorArrayPropertyException::class);
        // given
        $content = ['key' => []];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = false;
        // when
        ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
    }

    public function testGetArrayWithNullButRequiredNotNullArray(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = false;
        // when
        ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
    }

    public function testGetArrayWithNullRequiredString(): void
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = true;
        // when
        ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
    }

    public function testGetArrayWithMissingAndDefaultString(): void
    {
        // given
        $content = [];
        $key = 'key';
        $required = false;
        $default = ['default'];
        $allowEmpty = true;
        // when
        $result = ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
        // then
        self::assertEquals($default, $result);
    }
}
