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

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Interface ExceptionListenerInterface
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 */
interface PathKernelExceptionListenerInterface
{
    public function onKernelException(GetResponseForExceptionEvent $event);
}
