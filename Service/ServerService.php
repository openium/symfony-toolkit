<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ServerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ServerService implements ServerServiceInterface
{
    /**
     * ServerService constructor.
     */
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * getBasePath
     * Get server base url
     *
     * @throws SuspiciousOperationException
     */
    public function getBasePath(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (is_null($request)) {
            return '';
        }

        $isSecure = $request->isSecure();
        $prefix = $isSecure ? 'https://' : 'http://';
        $host = $request->getHost();
        $port = $request->getPort();
        $defaultPort = $isSecure ? 443 : 80;

        $portSuffix = ($port !== null && $port !== $defaultPort) ? ':' . $port : '';

        return $prefix . $host . $portSuffix . '/';
    }
}
