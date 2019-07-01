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

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Interface AuthenticationFailureListenerInterface
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 */
interface AuthenticationFailureListenerInterface
{
    public function onSymfonyAuthenticationFailure(AuthenticationFailureEvent $event);
}
