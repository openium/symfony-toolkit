<?php

/**
 * OpeniumSymfonyToolKit bundle
 *
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle;

use Openium\SymfonyToolKitBundle\DependencyInjection\OpeniumSymfonyToolKitExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpeniumSymfonyToolKitBundle
 *
 * @package Openium\SymfonyToolKitBundle
 */
class OpeniumSymfonyToolKitBundle extends Bundle
{
    /**
     * getContainerExtension
     *
     * @return ExtensionInterface|null
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new OpeniumSymfonyToolKitExtension();
    }
}
