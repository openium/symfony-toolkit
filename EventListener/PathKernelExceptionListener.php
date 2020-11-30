<?php

/**
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\EventListener;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class PathExceptionListener
 *
 * @package Openium\SymfonyToolKitBundle\EventListener
 */
class PathKernelExceptionListener implements PathKernelExceptionListenerInterface
{
    /**
     * @var ExceptionFormatServiceInterface
     */
    protected $exceptionFormat;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    private $enable;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * @throws \UnexpectedValueException
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->isEnable()) {
            if (strpos($event->getRequest()->getRequestUri(), $this->path) !== false) {
                if (method_exists($event, 'getThrowable')) {
                    $exception = $event->getThrowable();
                } else {
                    $exception = $event->getException();
                }
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
