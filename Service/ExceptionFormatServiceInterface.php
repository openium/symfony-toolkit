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
}
