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

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Interface ExceptionListenerInterface
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 */
interface PathKernelExceptionListenerInterface
{
    public function onKernelException(ExceptionEvent $event);
}
