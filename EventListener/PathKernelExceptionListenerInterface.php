<?php

namespace Openium\SymfonyToolKitBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Interface ExceptionListenerInterface
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 */
interface PathKernelExceptionListenerInterface
{
    public function onKernelException(ExceptionEvent $event): void;
}
