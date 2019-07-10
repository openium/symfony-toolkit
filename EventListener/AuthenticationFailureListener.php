<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Class AuthenticationFailureListener
 *
 * @package Openium\SymfonyToolKitBundle\EventListener
 */
class AuthenticationFailureListener implements AuthenticationFailureListenerInterface
{
    /**
     * @var bool
     */
    private $enable;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExceptionListener constructor.
     *
     * @param bool $enable
     * @param LoggerInterface $logger
     */
    public function __construct(bool $enable, LoggerInterface $logger)
    {
        $this->enable = $enable;
        $this->logger = $logger;
    }

    /**
     * Getter for enable
     *
     * @return bool
     */
    protected function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @return void
     * @throws HttpException
     *
     */
    public function onSymfonyAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        if ($this->isEnable()) {
            $exception = $event->getAuthenticationException();
            $strposExceptionFileName = strpos(
                $exception->getFile(),
                'Symfony/Component/Security/Core/Authentication/Provider/UserAuthenticationProvider.php'
            );
            if ($strposExceptionFileName === false) {
                $this->logger->debug(
                    sprintf(
                        'SymfonyToolKitBundle onSymfonyAuthenticationFailure : % %',
                        Response::HTTP_UNAUTHORIZED,
                        $exception->getMessage()
                    )
                );
                throw new HttpException(
                    Response::HTTP_UNAUTHORIZED,
                    $exception->getMessage()
                );
            }
        }
    }
}
