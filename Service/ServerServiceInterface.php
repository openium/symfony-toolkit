<?php

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
