<?php

namespace Openium\SymfonyToolKitBundle\EventListener;

use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use UnexpectedValueException;

/**
 * Class PathExceptionListener
 *
 * @package Openium\SymfonyToolKitBundle\EventListener
 */
class PathKernelExceptionListener implements PathKernelExceptionListenerInterface
{
    /**
     * ExceptionListener constructor.
     */
    public function __construct(
        protected ExceptionFormatServiceInterface $exceptionFormatService,
        protected string $path,
        private readonly bool $enable,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Getter for enable
     */
    protected function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * onKernelException
     *
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    #[\Override]
    public function onKernelException(ExceptionEvent $exceptionEvent): void
    {
        if ($this->isEnable() && str_contains($exceptionEvent->getRequest()->getRequestUri(), $this->path)) {
            $exception = $exceptionEvent->getThrowable();
            $response = $this->exceptionFormatService->formatExceptionResponse($exception);
            $code = $response->getStatusCode();
            $this->logger->debug(
                sprintf(
                    'SymfonyToolKitBundle onKernelException : %s %s',
                    $code,
                    $exception->getMessage()
                )
            );
            if ($code === Response::HTTP_INTERNAL_SERVER_ERROR) {
                $this->logger->critical($exception);
            } elseif ($code !== Response::HTTP_UNAUTHORIZED && $code !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($exception);
            } else {
                $this->logger->info($exception);
            }

            $exceptionEvent->setResponse($response);
        }
    }
}
