<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use DateTimeInterface;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorArrayPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorBooleanPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorDateFormatException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorFloatPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorIntegerPropertyException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorMissingParameterException;
use Openium\SymfonyToolKitBundle\Exception\ContentExtractorStringPropertyException;

/**
 * Class ContentExtractorUtils
 *
 * @package Openium\SymfonyToolKitBundle\Utils
 */
class ContentExtractorUtils
{
    /**
     * checkKeyExists
     *
     * @param array $content
     * @param string $key
     * @param bool|null $nullable
     *
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyExists(array $content, string $key, ?bool $nullable = false): void
    {
        if (!array_key_exists($key, $content) || (!$nullable && !isset($content[$key]))) {
            throw new ContentExtractorMissingParameterException($key);
        }
    }

    /**
     * checkKeyIsString
     *
     * @param array $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorStringPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyIsString(array $content, string $key, bool $nullable = false): void
    {
        self::checkKeyExists($content, $key, $nullable);
        if (
            array_key_exists($key, $content)
            && (
                (!$nullable && $content[$key] === null)
                || ($content[$key] !== null && !is_string($content[$key]))
            )
        ) {
            throw new ContentExtractorStringPropertyException($key);
        }
    }

    /**
     * checkKeyIsBoolean
     *
     * @param array $content
     * @param string $key
     *
     * @throws ContentExtractorBooleanPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyIsBoolean(array $content, string $key): void
    {
        self::checkKeyExists($content, $key);
        if (!is_bool($content[$key])) {
            throw new ContentExtractorBooleanPropertyException($key);
        }
    }

    /**
     * checkKeyIsInt
     *
     * @param array $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorIntegerPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyIsInt(array $content, string $key, bool $nullable = false): void
    {
        self::checkKeyExists($content, $key, $nullable);
        if (
            array_key_exists($key, $content)
            && (
                (!$nullable && $content[$key] === null)
                || ($content[$key] !== null && !is_int($content[$key]))
            )
        ) {
            throw new ContentExtractorIntegerPropertyException($key);
        }
    }

    /**
     * checkKeyIsFloat
     *
     * @param array $content
     * @param string $key
     * @param bool $nullable
     *
     * @throws ContentExtractorFloatPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyIsFloat(array $content, string $key, bool $nullable = false): void
    {
        self::checkKeyExists($content, $key, $nullable);
        if (
            array_key_exists($key, $content)
            && (
                (!$nullable && $content[$key] === null)
                || ($content[$key] !== null && !is_float($content[$key]))
            )
        ) {
            throw new ContentExtractorFloatPropertyException($key);
        }
    }

    /**
     * checkKeyIsArray
     *
     * @param array $content
     * @param string $key
     * @param bool $allowEmpty
     *
     * @throws ContentExtractorArrayPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return void
     */
    public static function checkKeyIsArray(array $content, string $key, bool $allowEmpty = false): void
    {
        self::checkKeyExists($content, $key, $allowEmpty);
        if (
            array_key_exists($key, $content)
            && (
                (!$allowEmpty && empty($content[$key]))
                || (!empty($content[$key]) && !is_array($content[$key]))
            )
        ) {
            throw new ContentExtractorArrayPropertyException($key);
        }
    }

    /**
     * getString
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param string|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorMissingParameterException
     * @throws ContentExtractorStringPropertyException
     * @return string|null
     */
    public static function getString(
        array $content,
        string $key,
        bool $required = true,
        ?string $default = null,
        bool $nullable = false,
        bool $convertToString = false
    ): ?string {
        try {
            self::checkKeyIsString($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        } catch (ContentExtractorStringPropertyException $exception) {
            if (!$convertToString) {
                throw $exception;
            }
        }
        return isset($content[$key]) ? strval($content[$key]) : null;
    }

    /**
     * getBool
     *
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param bool|null $default
     *
     * @throws ContentExtractorBooleanPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return bool|null
     */
    public static function getBool(array $content, string $key, bool $required = true, ?bool $default = true): ?bool
    {
        try {
            self::checkKeyIsBoolean($content, $key);
        } catch (ContentExtractorMissingParameterException $exception) {
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
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param int|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorIntegerPropertyException
     * @throws ContentExtractorMissingParameterException
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
        } catch (ContentExtractorMissingParameterException $exception) {
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
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param float|null $default
     * @param bool $nullable
     *
     * @throws ContentExtractorFloatPropertyException
     * @throws ContentExtractorMissingParameterException
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
        } catch (ContentExtractorMissingParameterException $exception) {
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
     * @param array $content
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
            self::checkKeyExists($content, $key, $nullable);
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
     * @param array $content
     * @param string $key
     * @param bool $required
     * @param array|null $default
     * @param bool $allowEmpty
     *
     * @throws ContentExtractorArrayPropertyException
     * @throws ContentExtractorMissingParameterException
     * @return array|null
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
        } catch (ContentExtractorMissingParameterException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }
        return $content[$key];
    }
}
