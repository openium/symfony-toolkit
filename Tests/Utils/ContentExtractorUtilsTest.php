<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use DateTimeImmutable;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorArrayPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorBooleanPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorDateFormatException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorFloatPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorIntegerPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorMissingParameterException;
use Openium\SymfonyToolKitBundle\Utils\ContentExtractorUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ContentExtractorUtilsTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Utils
 */
class ContentExtractorUtilsTest extends TestCase
{
    // checkKeyIsBoolean
    public function testCheckKeyNotEmptyWithoutKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyNotEmpty($content, $key, $nullable);
    }

    public function testCheckKeyNotEmptyWithNullKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyNotEmpty($content, $key, $nullable);
    }

    public function testCheckKeyNotEmptyWithNullKeyAndNullable()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyNotEmpty($content, $key, $nullable);
        } catch (BadRequestHttpException $e) {
            self::fail('BadRequestHttpException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyNotEmptyWithEmptyStringKey()
    {
        self::expectException(ContentExtractorMissingParameterException::class);
        // given
        $content = ['key' => ''];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyNotEmpty($content, $key, $nullable);
    }

    public function testCheckKeyNotEmptyWithStringKey()
    {
        // given
        $content = ['key' => 'value'];
        $key = 'key';
        $nullable = false;
        // when
        try {
            ContentExtractorUtils::checkKeyNotEmpty($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $e) {
            self::fail('ContentExtractorMissingParameterException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsBooleanWithoutKey()
    {
        self::expectException(ContentExtractorBooleanPropertyException::class);
        // given
        $content = [];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    // checkKeyIsBoolean
    public function testCheckKeyIsBooleanWithEmptyStringKey()
    {
        self::expectException(ContentExtractorBooleanPropertyException::class);
        // given
        $content = ['key' => ''];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    public function testCheckKeyIsBooleanWithNumberKey()
    {
        self::expectException(ContentExtractorBooleanPropertyException::class);
        // given
        $content = ['key' => 5];
        $key = 'key';
        // when
        ContentExtractorUtils::checkKeyIsBoolean($content, $key);
    }

    public function testCheckKeyIsBooleanWithBoolKey()
    {
        // given
        $content = ['key' => true];
        $key = 'key';
        // when
        try {
            ContentExtractorUtils::checkKeyIsBoolean($content, $key);
        } catch (ContentExtractorBooleanPropertyException $e) {
            self::fail('ContentExtractorBooleanPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // checkKeyIsInt
    public function testCheckKeyIsIntWithoutKey()
    {
        self::expectException(ContentExtractorIntegerPropertyException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
    }

    public function testCheckKeyIsIntWithNullKey()
    {
        self::expectException(ContentExtractorIntegerPropertyException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
    }

    public function testCheckKeyIsIntWithNullKeyAndNullbale()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
        } catch (ContentExtractorIntegerPropertyException $e) {
            self::fail('ContentExtractorIntegerPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsIntWithInt()
    {
        // given
        $content = ['key' => 5];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsInt($content, $key, $nullable);
        } catch (ContentExtractorIntegerPropertyException $e) {
            self::fail('ContentExtractorIntegerPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsIntWithFloat()
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
    public function testCheckKeyIsFloatWithoutKey()
    {
        self::expectException(ContentExtractorFloatPropertyException::class);
        // given
        $content = [];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithNullKey()
    {
        self::expectException(ContentExtractorFloatPropertyException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = false;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithNullKeyAndNullbale()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorFloatPropertyException $e) {
            self::fail('ContentExtractorFloatPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    public function testCheckKeyIsFloatWithInt()
    {
        self::expectException(ContentExtractorFloatPropertyException::class);
        // given
        $content = ['key' => 5];
        $key = 'key';
        $nullable = true;
        // when
        ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
    }

    public function testCheckKeyIsFloatWithFloat()
    {
        // given
        $content = ['key' => 5.5];
        $key = 'key';
        $nullable = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorFloatPropertyException $e) {
            self::fail('ContentExtractorFloatPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // checkKeyIsArray
    public function testcheckKeyIsArrayWithString()
    {
        self::expectException(ContentExtractorArrayPropertyException::class);
        // given
        $content = ['key' => 'not an array'];
        $key = 'key';
        $allowEmpty = true;
        // when
        ContentExtractorUtils::checkKeyIsArray($content, $key, $allowEmpty);
    }

    public function testcheckKeyIsArrayWithArray()
    {
        // given
        $content = ['key' => ['an array']];
        $key = 'key';
        $allowEmpty = true;
        // when
        try {
            ContentExtractorUtils::checkKeyIsArray($content, $key, $allowEmpty);
        } catch (ContentExtractorArrayPropertyException $e) {
            self::fail('ContentExtractorArrayPropertyException');
        }
        // then
        self::assertTrue(true);
    }

    // getString
    public function testGetStringWithRequiredNotNullString()
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

    public function testGetStringWithMissingButRequiredNotNullString()
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

    public function testGetStringWithNullButRequiredNotNullString()
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

    public function testGetStringWithNullRequiredString()
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

    public function testGetStringWithMissingAndDefaultString()
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
    public function testGetBoolWithRequiredNotNull()
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

    public function testGetBoolNullWithNutRequiredAndDefault()
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
    public function testGetIntWithRequiredNotNullInt()
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

    public function testGetIntWithStringRequiredNotNullInt()
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

    public function testGetIntWithMissingDefault()
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

    public function testGetIntWithNull()
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
    public function testGetFloatWithRequiredNotNullInt()
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

    public function testGetFloatWithStringRequiredNotNullInt()
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

    public function testGetFloatWithMissingDefault()
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

    public function testGetFloatWithNull()
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
    public function testGetDateTimeInterfaceWithStringRequiredNotNull()
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

    public function testGetDateTimeInterfaceWithShortStringRequiredNotNull()
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

    public function testGetDateTimeInterfaceBadFormatWithStringRequiredNotNull()
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

    public function testGetDateTimeInterfaceNullWithStringRequiredNotNull()
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

    public function testGetDateTimeInterfaceNullWithStringNotRequiredNullable()
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

    public function testGetDateTimeInterfaceNullWithDefaultStringNotRequiredNotNull()
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
    public function testGetArrayWithRequiredArray()
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

    public function testGetArrayWithRequiredArrayButEmptyArray()
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

    public function testGetArrayWithNullButRequiredNotNullArray()
    {
        self::expectException(ContentExtractorArrayPropertyException::class);
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = false;
        // when
        ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
    }

    public function testGetArrayWithNullRequiredString()
    {
        // given
        $content = ['key' => null];
        $key = 'key';
        $required = true;
        $default = null;
        $allowEmpty = true;
        // when
        $result = ContentExtractorUtils::getArray($content, $key, $required, $default, $allowEmpty);
        // then
        self::assertNull($result);
    }

    public function testGetArrayWithMissingAndDefaultString()
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
