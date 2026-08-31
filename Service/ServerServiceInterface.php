<?php

namespace Openium\SymfonyToolKitBundle\Service;

/**
 * Interface ServerServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 *
 * @deprecated since 7.0, will be removed in 8.0. Use
 *             {@see \Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost()} instead.
 */
interface ServerServiceInterface
{
    /**
     * Get server base url
     */
    public function getBasePath(): string;
}
