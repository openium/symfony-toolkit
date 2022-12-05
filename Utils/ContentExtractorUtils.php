<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use DateTimeInterface;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorArrayPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorBooleanPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorDateFormatException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorFloatPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorIntegerPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorMissingParameterException;

/**
 * Class ContentExtractorUtils
 *
 * @package Openium\SymfonyToolKitBundle\Utils
 */
class ContentExtractorUtils
{
    /**
     * checkKeyNotEmpty
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyNotEmpty(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (
                !$nullable && is_null($content[$key])
            )
        ) {
            throw new ContentExtractorMissingParameterException($key);
        }
    }

    /**
     * checkKeyIsBoolean
     *
     * @param array<string, mixed> $content
     * @param string $key
     *
     * @throws ContentExtractorBooleanPropertyException
     * @return void
     */
    public static function checkKeyIsBoolean(array $content, string $key): void
    {
        if (!array_key_exists($key, $content) || !is_bool($content[$key])) {
            throw new ContentExtractorBooleanPropertyException($key);
        }
    }

    /**
     * checkKeyIsInt
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorIntegerPropertyException
     * @return void
     */
    public static function checkKeyIsInt(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$nullable && !is_int($content[$key]))
            || ($nullable && !is_null($content[$key]) && !is_int($content[$key]))
        ) {
            throw new ContentExtractorIntegerPropertyException($key);
        }
    }

    /**
     * checkKeyIsFloat
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorFloatPropertyException
     * @return void
     */
    public static function checkKeyIsFloat(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$nullable && !is_float($content[$key]))
            || ($nullable && !is_null($content[$key]) && !is_float($content['key']))
        ) {
            throw new ContentExtractorFloatPropertyException($key);
        }
    }

    /**
     * checkKeyIsArray
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $allowEmpty
     *
     * @throws ContentExtractorArrayPropertyException
     * @return void
     */
    public static function checkKeyIsArray(array $content, string $key, bool $allowEmpty = false): void
    {
        if (
            !array_key_exists($key, $content)
            || !is_array($content[$key])
            || (!$allowEmpty && count($content[$key]) === 0)
        ) {
            throw new ContentExtractorArrayPropertyException();
        }
    }

    /**
     * getString
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param string|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorMissingParameterException
     * @return string|null
     */
    public static function getString(
        array $content,
        string $key,
        bool $required = true,
        ?string $default = null,
        bool $nullable = false
    ): ?string {
        try {
            self::checkKeyNotEmpty($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] !== null) {
            return strval($content[$key]);
        } else {
            return null;
        }
    }

    /**
     * getBool
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param bool|null $default
     *
     * @throws ContentExtractorBooleanPropertyException
     * @return bool|null
     */
    public static function getBool(array $content, string $key, bool $required = true, ?bool $default = true): ?bool
    {
        try {
            self::checkKeyIsBoolean($content, $key);
        } catch (ContentExtractorBooleanPropertyException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getInt
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param int|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorIntegerPropertyException
     * @return int|null
     */
    public static function getInt(
        array $content,
        string $key,
        bool $required = true,
        ?int $default = 0,
        bool $nullable = false
    ): ?int {
        try {
            self::checkKeyIsInt($content, $key, $nullable);
        } catch (ContentExtractorIntegerPropertyException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getFloat
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param float|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorFloatPropertyException
     * @return float|null
     */
    public static function getFloat(
        array $content,
        string $key,
        bool $required = true,
        ?float $default = 0.0,
        bool $nullable = false
    ): ?float {
        try {
            self::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorFloatPropertyException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }

    /**
     * getDateTimeInterface
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param DateTimeInterface|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorMissingParameterException
     * @throws ContentExtractorDateFormatException
     * @return DateTimeInterface|null
     */
    public static function getDateTimeInterface(
        array $content,
        string $key,
        bool $required = true,
        ?DateTimeInterface $default = null,
        bool $nullable = false
    ): ?DateTimeInterface {
        try {
            self::checkKeyNotEmpty($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] === null) {
            return null;
        }
        $dateTimeResult = DateStringUtils::getDateTimeFromString($content[$key]);
        // => false if wrong date format
        if ($dateTimeResult === false) {
            throw new ContentExtractorDateFormatException($key);
        }
        return $dateTimeResult;
    }

    /**
     * getArray
     *
     * @param array<string, mixed> $content
     * @param string $key
     * @param bool $required
     * @param array<string|int, mixed>|null $default
     * @param bool $allowEmpty
     *
     * @throws ContentExtractorArrayPropertyException
     * @return array<string|int, mixed>|null
     */
    public static function getArray(
        array $content,
        string $key,
        bool $required = true,
        ?array $default = [],
        bool $allowEmpty = true
    ): ?array {
        try {
            self::checkKeyIsArray($content, $key, $allowEmpty);
        } catch (ContentExtractorArrayPropertyException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        if ($content[$key] !== null) {
            return $content[$key];
        } else {
            return null;
        }
    }
}
