<?php
/**
 * ExceptionFormatServiceInterface
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnexpectedValueException;

/**
 * Interface ExceptionFormatServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface ExceptionFormatServiceInterface
{
    /**
     * formatExceptionResponse
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function formatExceptionResponse(Throwable $exception): Response;

    /**
     * getArray
     *
     * @return array<string, mixed>
     */
    public function getArray(
        Exception $exception,
        ?int $code = null,
        ?string $text = null,
        ?string $message = null
    ): array;

    /**
     * getStatusCode
     */
    public function getStatusCode(Exception $exception): int;

    /**
     * getStatusText
     */
    public function getStatusText(Exception $exception): string;

    /**
     * genericExceptionResponse
     * @return array<string|int> [code, text, message]
     */
    public function genericExceptionResponse(Exception $exception): array;
}
