<?php

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
     * @return array<string, int|string|array<string|int, mixed>|null>
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
     * @param array<string, int|string|array<string|int, mixed>|null> $error
     *
     * @return array<string, int|string|array<string|int, mixed>|null>
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
     *
     * @return array{0: int, 1: string, 2:string|null} [code, text, message]
     */
    public function genericExceptionResponse(Exception $exception): array;
}
