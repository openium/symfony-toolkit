<?php

/**
 * ServerServiceInterface
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

/**
 * Interface ServerServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface ServerServiceInterface
{
    public function getBasePath(): string;
}
