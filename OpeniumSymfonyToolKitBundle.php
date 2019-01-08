<?php

/**
 * OpeniumSymfonyToolKit bundle
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle;

use Openium\SymfonyToolKitBundle\DependencyInjection\OpeniumSymfonyToolKitExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class OpeniumSymfonyToolKitBundle
 *
 * @package Openium\SymfonyToolKitBundle
 */
class OpeniumSymfonyToolKitBundle extends Bundle
{
    /**
     * @return OpeniumSymfonyToolKitExtension
     */
    public function getContainerExtension()
    {
        return new OpeniumSymfonyToolKitExtension();
    }
}
