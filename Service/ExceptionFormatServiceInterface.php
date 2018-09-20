<?php

/**
 * ExceptionFormatServiceInterface
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use \Symfony\Component\HttpFoundation\Response;

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
     * @param \Exception $exception
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return Response
     */
    public function formatExceptionResponse(\Exception $exception): Response;

    /**
     * getArray
     *
     * @param \Exception $exception
     * @param null $code
     * @param null $text
     * @param null $message
     *
     * @return array
     */
    public function getArray(\Exception $exception, $code = null, $text = null, $message = null): array;

    /**
     * getStatusCode
     *
     * @param \Exception $exception
     *
     * @return int
     */
    public function getStatusCode(\Exception $exception);

    /**
     * getStatusText
     *
     * @param \Exception $exception
     *
     * @return string
     */
    public function getStatusText(\Exception $exception);
}
