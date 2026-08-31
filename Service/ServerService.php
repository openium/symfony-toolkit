<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ServerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 *
 * @deprecated since 7.0, will be removed in 8.0. Use
 *             {@see Request::getSchemeAndHttpHost()} instead (append '/' to match
 *             the trailing slash returned by getBasePath()).
 */
class ServerService implements ServerServiceInterface
{
    /**
     * ServerService constructor.
     */
    public function __construct(private readonly RequestStack $requestStack)
    {
        trigger_deprecation(
            'openium/symfony-toolkit',
            '7.0',
            'The "%s" class is deprecated and will be removed in 8.0, use "%s::getSchemeAndHttpHost()" instead.',
            self::class,
            Request::class
        );
    }

    /**
     * getBasePath
     * Get server base url
     *
     * @throws SuspiciousOperationException
     */
    #[\Override]
    public function getBasePath(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (is_null($request)) {
            return '';
        }

        return $request->getSchemeAndHttpHost() . '/';
    }
}
