<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\EventListener;

use InvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;
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
        protected ExceptionFormatServiceInterface $exceptionFormat,
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
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->isEnable() && str_contains($event->getRequest()->getRequestUri(), $this->path)) {
            $exception = $event->getThrowable();
            $response = $this->exceptionFormat->formatExceptionResponse($exception);
            $code = $response->getStatusCode();
            $this->logger->debug(
                sprintf(
                    'SymfonyToolKitBundle onKernelException : %s %s',
                    $code,
                    $exception->getMessage()
                )
            );
            // setResponse() stops the event propagation, so Symfony's ErrorListener never gets to
            // log the exception: log it here, the same way it would have.
            $this->logException($exception, $response);
            $event->setResponse($response);
        }
    }

    /**
     * Logs the exception like Symfony's ErrorListener::logException() does: the throwable goes in
     * the `exception` context key, so Monolog formatters (JSON, ELK...) get its class, file, line
     * and stack trace.
     *
     * 5xx responses are logged as critical. Every other status (4xx) is logged as a warning: high
     * enough to reach handlers starting at notice, low enough not to trip a fingers_crossed
     * handler with an `error` action level on every 401 or 404.
     */
    protected function logException(Throwable $exception, Response $response): void
    {
        $level = $response->getStatusCode() >= Response::HTTP_INTERNAL_SERVER_ERROR
            ? LogLevel::CRITICAL
            : LogLevel::WARNING;
        $this->logger->log(
            $level,
            sprintf(
                'Uncaught PHP Exception %s: "%s" at %s line %s',
                get_debug_type($exception),
                $exception->getMessage(),
                basename($exception->getFile()),
                $exception->getLine()
            ),
            ['exception' => $exception]
        );
    }
}
