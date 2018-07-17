<?php

/**
 * OpeniumSymfonyToolKit bundle
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
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
