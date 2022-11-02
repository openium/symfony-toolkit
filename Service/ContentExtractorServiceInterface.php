<?php

namespace Openium\SymfonyToolKitBundle\Service;

use DateTimeInterface;

/**
 * Interface ContentExtractorServiceInterface
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
interface ContentExtractorServiceInterface
{
    public function checkKeyNotEmpty(array $content, string $key, ?bool $nullable = false): void;

    public function checkKeyIsBoolean(array $content, string $key): void;

    public function checkKeyIsInt(array $content, string $key, bool $nullable = false): void;

    public function checkKeyIsFloat(array $content, string $key, bool $nullable = false): void;

    public function checkKeyIsArray(array $content, string $key, bool $allowEmpty = false): void;

    public function getString(
        array $content,
        string $key,
        bool $required = true,
        ?string $default = null,
        bool $nullable = false
    ): ?string;

    public function getBool(array $content, string $key, bool $required = true, ?bool $default = true): ?bool;

    public function getInt(
        array $content,
        string $key,
        bool $required = true,
        ?int $default = 0,
        bool $nullable = false
    ): ?int;

    public function getFloat(
        array $content,
        string $key,
        bool $required = true,
        ?float $default = 0.0,
        bool $nullable = false
    ): ?float;

    public function getDateTimeInterface(
        array $content,
        string $key,
        bool $required = true,
        ?DateTimeInterface $default = null,
        bool $nullable = false
    ): ?DateTimeInterface;

    public function getArray(
        array $content,
        string $key,
        bool $required = true,
        ?array $default = [],
        bool $allowEmpty = true
    ): ?array;
}
