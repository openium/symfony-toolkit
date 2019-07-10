<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\EventListener;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

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
     * @param GetResponseForExceptionEvent $event
     *
     * @return void
     * @throws \UnexpectedValueException
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->isEnable()) {
            if (strpos($event->getRequest()->getRequestUri(), $this->path) !== false) {
                $exception = $event->getException();
                $response = $this->exceptionFormat->formatExceptionResponse($exception);
                $this->logger->debug(
                    sprintf(
                        'SymfonyToolKitBundle onKernelException : % %',
                        $response->getStatusCode(),
                        $exception->getMessage()
                    )
                );
                $event->setResponse($response);
            }
        }
    }
}
