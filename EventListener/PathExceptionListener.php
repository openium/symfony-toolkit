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

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class PathExceptionListener
 *
 * @package Openium\SymfonyToolKitBundle\EventListener
 */
class PathExceptionListener implements ExceptionListenerInterface
{
    /**
     * @var ExceptionFormatServiceInterface
     */
    protected $exceptionFormat;

    /**
     * @var string
     */
    protected $path;
    /**
     * @var bool
     */
    private $enable;

    /**
     * ExceptionListener constructor.
     *
     * @param ExceptionFormatServiceInterface $exceptionFormat
     * @param string $path
     * @param bool $enable
     */
    public function __construct(ExceptionFormatServiceInterface $exceptionFormat, string $path, bool $enable)
    {
        $this->exceptionFormat = $exceptionFormat;
        $this->path = $path;
        $this->enable = $enable;
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
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($this->isEnable()) {
            $exception = $event->getException();
            if (strpos($event->getRequest()->getRequestUri(), $this->path) !== false) {
                $response = $this->exceptionFormat->formatExceptionResponse($exception);
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @throws HttpException
     *
     * @return void
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
                throw new HttpException(
                    Response::HTTP_UNAUTHORIZED,
                    $exception->getMessage()
                );
            }
        }
    }
}
