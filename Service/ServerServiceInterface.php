<?php

/**
 * ServerServiceInterface
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

/**
 * Interface ServerServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface ServerServiceInterface
{
    /**
     * Get server base url
     *
     * @return string
     */
    public function getBasePath(): string;
}
