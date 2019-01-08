<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener;

use Openium\SymfonyToolKitBundle\EventListener\PathExceptionListener;

/**
 * Class TestExceptionListener
 *
 * @package Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener
 */
class TestExceptionListener extends PathExceptionListener
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
