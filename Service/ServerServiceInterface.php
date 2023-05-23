<?php
/**
 * ServerServiceInterface
 * PHP Version >=8.0
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
     */
    public function getBasePath(): string;
}
