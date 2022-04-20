<?php

/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener;

use Openium\SymfonyToolKitBundle\EventListener\AuthenticationFailureSubscriber;

/**
 * Class TestAuthenticationFailureListener
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 */
class TestAuthenticationFailureSubscriber extends AuthenticationFailureSubscriber
{
    /**
     * getEnable
     *
     * @return bool
     */
    public function getEnable(): bool
    {
        return $this->isEnable();
    }
}
