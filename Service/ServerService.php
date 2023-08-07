<?php
/**
 * ServerService
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

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
        $prefix = $request->isSecure() ? 'https://' : 'http://';
        $host = $request->getHost();
        return $prefix . $host . '/';
    }
}
