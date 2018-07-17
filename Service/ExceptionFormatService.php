<?php

/**
 * ExceptionFormatService
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ExceptionFormatService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ExceptionFormatService implements ExceptionFormatServiceInterface
{
    protected const DEFAULT_STATUS_TEXT = 'Internal Server Error';

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * ExceptionFormatService constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param \Exception $exception
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     *
     * @return Response
     */
    public function formatExceptionResponse(\Exception $exception): Response
    {
        if (is_a($exception, "Symfony\Component\Security\Core\Exception\AuthenticationException")) {
            $code = Response::HTTP_UNAUTHORIZED;
            $text = Response::$statusTexts[$code];
            $message = $text;
        } elseif (is_a($exception, "Firebase\Auth\Token\Exception\ExpiredToken")
            || is_a($exception, "Firebase\Auth\Token\Exception\IssuedInTheFuture")
            || is_a($exception, "Firebase\Auth\Token\Exception\InvalidToken")) {
            // Firebase part
            $code = Response::HTTP_UNAUTHORIZED;
            $text = $exception->getMessage();
            $message = $text;
        } else {
            $code = $this->getStatusCode($exception);
            $text = $this->getStatusText($exception);
            $message = null;
        }
        $error = $this->getArray($exception, $code, $text, $message);
        $response = new JsonResponse();
        $response->setStatusCode($code ?: $this->getStatusCode($exception));
        $response->setContent((json_encode($error)) ?: '');
        return $response;
    }

    /**
     * @param \Exception $exception
     * @param null $code
     * @param null $text
     * @param null $message
     *
     * @return array
     */
    public function getArray(\Exception $exception, $code = null, $text = null, $message = null): array
    {
        $error = [];
        $error['status_code'] = $code ?? $this->getStatusCode($exception);
        $error['status_text'] = $text ?? $this->getStatusText($exception);
        $error['message'] = $message ?? $exception->getMessage();
        // Stripe part
        if ($error['status_code'] == Response::HTTP_PAYMENT_REQUIRED
            && is_a($exception->getPrevious(), "Stripe\Error\Card")) {
            $body = $exception->getPrevious()->getJsonBody();
            $err = $body['error'];
            $error['type'] = $err['type'];
            $error['code'] = $err['code'];
        }
        if ($this->kernel->getEnvironment() != 'prod') {
            $error['trace'] = $exception->getTrace();
        }

        return $error;
    }

    /**
     * @param \Exception $exception
     *
     * @return int
     */
    public function getStatusCode(\Exception $exception)
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }
        if (is_a($exception, "Symfony\Component\Security\Core\Exception\AccessDeniedException")) {
            return Response::HTTP_UNAUTHORIZED;
        }
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * @param \Exception $exception
     *
     * @return string
     */
    public function getStatusText(\Exception $exception)
    {
        $code = $this->getStatusCode($exception);
        if ($code == Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        } else {
            return array_key_exists(
                $code,
                Response::$statusTexts
            ) ? Response::$statusTexts[$code] : self::DEFAULT_STATUS_TEXT;
        }
    }
}
