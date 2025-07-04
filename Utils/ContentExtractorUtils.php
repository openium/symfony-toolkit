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
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorMissingParameterException
     */
    public static function checkKeyExists(array $content, string $key, bool $nullable = false): void
    {
        if (
            !array_key_exists($key, $content)
            || (!$nullable && $content[$key] === null)
        ) {
            throw new ContentExtractorMissingParameterException($key);
        }
    }

    /**
     * checkKeyIsString
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorMissingParameterException
     * @throws ContentExtractorStringPropertyException
     */
    public static function checkKeyIsString(
        array $content,
        string $key,
        bool $nullable = false
    ): void {
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
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorBooleanPropertyException
     * @throws ContentExtractorMissingParameterException
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
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorIntegerPropertyException
     * @throws ContentExtractorMissingParameterException
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
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorFloatPropertyException
     * @throws ContentExtractorMissingParameterException
     */
    public static function checkKeyIsFloat(
        array $content,
        string $key,
        bool $nullable = false
    ): void {
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
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorArrayPropertyException
     * @throws ContentExtractorMissingParameterException
     */
    public static function checkKeyIsArray(
        array $content,
        string $key,
        bool $allowEmpty = false
    ): void {
        if (
            !array_key_exists($key, $content)
            || $content[$key] === null
        ) {
            throw new ContentExtractorMissingParameterException($key);
        }

        if (
            !is_array($content[$key])
            || (!$allowEmpty && $content[$key] === [])
        ) {
            throw new ContentExtractorArrayPropertyException($key);
        }
    }

    /**
     * getString
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorMissingParameterException
     * @throws ContentExtractorStringPropertyException
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

        /** @phpstan-ignore-next-line */
        return isset($content[$key]) ? trim((string)$content[$key]) : null;
    }

    /**
     * getBool
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorBooleanPropertyException
     * @throws ContentExtractorMissingParameterException
     */
    public static function getBool(
        array $content,
        string $key,
        bool $required = true,
        ?bool $default = true
    ): ?bool {
        try {
            self::checkKeyIsBoolean($content, $key);
        } catch (ContentExtractorMissingParameterException $contentExtractorMissingParameterException) {
            if ($required) {
                throw $contentExtractorMissingParameterException;
            } else {
                return $default;
            }
        }

        /** @phpstan-ignore-next-line */
        return $content[$key];
    }

    /**
     * getInt
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorIntegerPropertyException
     * @throws ContentExtractorMissingParameterException
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
        } catch (ContentExtractorMissingParameterException $contentExtractorMissingParameterException) {
            if ($required) {
                throw $contentExtractorMissingParameterException;
            } else {
                return $default;
            }
        }

        /** @phpstan-ignore-next-line */
        return $content[$key];
    }

    /**
     * getFloat
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorFloatPropertyException
     * @throws ContentExtractorMissingParameterException
     */
    public static function getFloat(
        array $content,
        string $key,
        bool $required = true,
        ?float $default = 0.0,
        bool $nullable = false,
        bool $acceptInt = false
    ): ?float {
        try {
            self::checkKeyIsFloat($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        } catch (ContentExtractorFloatPropertyException $exception) {
            if ($acceptInt) {
                try {
                    return (float)self::getInt($content, $key, $required, (int)$default, $nullable);
                } catch (ContentExtractorIntegerPropertyException | ContentExtractorMissingParameterException) {
                    throw $exception;
                }
            }

            throw $exception;
        }

        /** @phpstan-ignore-next-line */
        return $content[$key];
    }

    /**
     * getDateTimeInterface
     *
     * @param array<string, mixed> $content
     *
     * @throws ContentExtractorMissingParameterException
     * @throws ContentExtractorDateFormatException
     */
    public
    static function getDateTimeInterface(
        array $content,
        string $key,
        bool $required = true,
        ?DateTimeInterface $default = null,
        bool $nullable = false
    ): ?DateTimeInterface {
        try {
            self::checkKeyExists($content, $key, $nullable);
        } catch (ContentExtractorMissingParameterException $contentExtractorMissingParameterException) {
            if ($required) {
                throw $contentExtractorMissingParameterException;
            } else {
                return $default;
            }
        }

        if ($content[$key] === null) {
            return null;
        }

        /** @phpstan-ignore-next-line */
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
     * @throws ContentExtractorMissingParameterException
     * @return array|null
     */
    /** @phpstan-ignore-next-line */
    public
    static function getArray(
        array $content,
        string $key,
        bool $required = true,
        ?array $default = [],
        bool $allowEmpty = true
    ): ?array {
        try {
            self::checkKeyIsArray($content, $key, $allowEmpty);
        } catch (ContentExtractorMissingParameterException | ContentExtractorArrayPropertyException $exception) {
            if ($required) {
                throw $exception;
            } else {
                return $default;
            }
        }

        return $content[$key];
    }
}
