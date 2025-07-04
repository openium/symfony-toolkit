<?php

namespace Openium\SymfonyToolKitBundle\Service;

/**
 * Interface AtHelperInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface AtHelperInterface
{
    public function executeAndCaptureOutput(string $cmd, int &$result): string|false;

    public function createAtCommandFromPath(string $cmd, int $timestamp, string $path, int &$result): false|string;

    /** @deprecated use createAtCommandFromPath instead */
    public function createAtCommand(string $cmd, int $timestamp, int &$result): false|string;

    public function formatTimestampForAt(int $timestamp): string;

    public function extractJobNumberFromAtOutput(string $output): ?string;

    public function removeAtCommand(string $atJobNumber): bool;
}
