<?php

/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\EventListener;

use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use UnexpectedValueException;

/**
 * Class PathExceptionListener
 *
 * @package Openium\SymfonyToolKitBundle\EventListener
 */
class PathKernelExceptionListener implements PathKernelExceptionListenerInterface
{
    protected ExceptionFormatServiceInterface $exceptionFormat;

    protected string $path;

    private bool $enable;

    private LoggerInterface $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param ExceptionFormatServiceInterface $exceptionFormat
     * @param string $path
     * @param bool $enable
     * @param LoggerInterface $logger
     */
    public function __construct(
        ExceptionFormatServiceInterface $exceptionFormat,
        string $path,
        bool $enable,
        LoggerInterface $logger
    ) {
        $this->exceptionFormat = $exceptionFormat;
        $this->path = $path;
        $this->enable = $enable;
        $this->logger = $logger;
    }

    /**
     * Getter for enable
     *
     * @return bool
     */
    protected function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     * @throws UnexpectedValueException
     *
     * @throws InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->isEnable()) {
            if (str_contains($event->getRequest()->getRequestUri(), $this->path)) {
                $exception = $event->getThrowable();
                $response = $this->exceptionFormat->formatExceptionResponse($exception);
                $code = $response->getStatusCode();
                $this->logger->debug(
                    sprintf(
                        'SymfonyToolKitBundle onKernelException : %s %s',
                        $code,
                        $exception->getMessage()
                    )
                );
                if ($code === Response::HTTP_INTERNAL_SERVER_ERROR) {
                    $this->logger->critical($exception);
                } elseif ($code !== Response::HTTP_UNAUTHORIZED && $code !== Response::HTTP_NOT_FOUND) {
                    $this->logger->error($exception);
                } else {
                    $this->logger->info($exception);
                }
                $event->setResponse($response);
            }
        }
    }
}
