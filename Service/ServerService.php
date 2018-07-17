<?php

/**
 * ServerService
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ServerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ServerService implements ServerServiceInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * ServerService constructor.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Get server base url
     *
     * @return string
     */
    public function getBasePath(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return '';
        }
        if ($request->isSecure()) {
            $prefix = 'https://';
        } else {
            $prefix = 'http://';
        }
        $host = $request->getHost();
        $basePath = $prefix . $host . '/';
        return $basePath;
    }
}
