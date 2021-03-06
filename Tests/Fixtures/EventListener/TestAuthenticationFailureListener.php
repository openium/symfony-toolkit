<?php

/**
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener;

use Openium\SymfonyToolKitBundle\EventListener\AuthenticationFailureListener;

/**
 * Class TestAuthenticationFailureListener
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 */
class TestAuthenticationFailureListener extends AuthenticationFailureListener
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
