<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

/**
 * Interface AtHelperInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface AtHelperInterface
{
    public function executeAndCaptureOutput($cmd, &$result);

    public function createAtCommand(string $cmd, int $timestamp, &$result);

    public function formatTimestampForAt(int $timestamp): string;

    public function extractJobNumberFromAtOutput($output): ?string;

    public function removeAtCommand(string $atJobNumber): bool;
}
