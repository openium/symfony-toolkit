<?php

/**
 * ExceptionFormatServiceInterface
 *
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
     * @param Throwable $exception
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     *
     * @return Response
     */
    public function formatExceptionResponse(Throwable $exception): Response;

    /**
     * getArray
     *
     * @param Exception $exception
     * @param int|null $code
     * @param string|null $text
     * @param string|null $message
     *
     * @return array<string, mixed>
     */
    public function getArray(Exception $exception, ?int $code = null, ?string $text = null, ?string $message = null): array;

    /**
     * getStatusCode
     *
     * @param Exception $exception
     *
     * @return int
     */
    public function getStatusCode(Exception $exception): int;

    /**
     * getStatusText
     *
     * @param Exception $exception
     *
     * @return string
     */
    public function getStatusText(Exception $exception): string;
}
