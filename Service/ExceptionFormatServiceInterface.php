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
     * addKeyToErrorArray
     * Used to add key in error array
     * All keys in array will be serialized and returned to client in a json object
     *
     * @param array $error
     * @param Exception $exception
     *
     * @return array
     */
    public function addKeyToErrorArray(array $error, Exception $exception): array;
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
     * must return array composed by error information returned to client
     * @return array<string|int> [code, text, message]
     */
    public function genericExceptionResponse(Exception $exception): array;
}
