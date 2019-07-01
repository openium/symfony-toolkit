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
     * ExceptionListener constructor.
     *
     * @param ExceptionFormatServiceInterface $exceptionFormat
     * @param string $path
     * @param bool $enable
     */
    public function __construct(ExceptionFormatServiceInterface $exceptionFormat, string $path, bool $enable)
    {
        $this->exceptionFormat = $exceptionFormat;
        $this->path = $path;
        $this->enable = $enable;
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
            $exception = $event->getException();
            if (strpos($event->getRequest()->getRequestUri(), $this->path) !== false) {
                $response = $this->exceptionFormat->formatExceptionResponse($exception);
                $event->setResponse($response);
            }
        }
    }
}
