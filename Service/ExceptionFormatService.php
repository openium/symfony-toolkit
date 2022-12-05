<?php
/**
 * ExceptionFormatService
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Throwable;

/**
 * Class ExceptionFormatService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class ExceptionFormatService implements ExceptionFormatServiceInterface
{
    protected KernelInterface $kernel;

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
     * formatExceptionResponse
     *
     * @param Throwable $exception
     *
     * @throws InvalidArgumentException
     * @return Response
     */
    public function formatExceptionResponse(Throwable $exception): Response
    {
        $response = new JsonResponse();
        if ($exception instanceof Exception) {
            if (is_a($exception, "Symfony\Component\Security\Core\Exception\AuthenticationException")) {
                $code = Response::HTTP_UNAUTHORIZED;
                $text = Response::$statusTexts[$code];
                $message = $text;
            } elseif (
                is_a($exception, "Firebase\Auth\Token\Exception\ExpiredToken")
                || is_a($exception, "Firebase\Auth\Token\Exception\IssuedInTheFuture")
                || is_a($exception, "Firebase\Auth\Token\Exception\InvalidToken")
            ) {
                // Firebase part
                $code = Response::HTTP_UNAUTHORIZED;
                /* @phpstan-ignore-next-line */
                $text = $exception->getMessage();
                $message = $text;
            } else {
                $code = $this->getStatusCode($exception);
                $text = $this->getStatusText($exception);
                $message = null;
            }
            $error = $this->getArray($exception, $code, $text, $message);
            $response->setStatusCode($code);
            $json = json_encode($error);
            $response->setContent(($json !== false) ? $json : '');
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent($exception->getMessage());
        }
        return $response;
    }

    /**
     * getArray
     *
     * @param Exception $exception
     * @param int|null $code
     * @param string|null $text
     * @param string|null $message
     *
     * @return array<string, mixed>
     */
    public function getArray(
        Exception $exception,
        ?int $code = null,
        ?string $text = null,
        ?string $message = null
    ): array {
        $error = [];
        $error['status_code'] = $code ?? $this->getStatusCode($exception);
        $error['status_text'] = $text ?? $this->getStatusText($exception);
        $error['message'] = $message ?? $exception->getMessage();
        // Stripe part
        if (
            $error['status_code'] == Response::HTTP_PAYMENT_REQUIRED
            && $exception->getPrevious() !== null
            && is_a($exception->getPrevious(), "Stripe\Error\Card")
        ) {
            /* @phpstan-ignore-next-line */
            $body = $exception->getPrevious()->getJsonBody();
            $err = $body['error'];
            $error['type'] = $err['type'];
            $error['code'] = $err['code'];
        }
        if ($this->kernel->getEnvironment() !== 'prod') {
            $error['trace'] = $exception->getTrace();
            $error['previous'] = [];
            if (!is_null($exception->getPrevious())) {
                $error['previous']['message'] = $exception->getPrevious()->getMessage();
                $error['previous']['code'] = $exception->getPrevious()->getCode();
            }
        }
        return $error;
    }

    /**
     * getStatusCode
     *
     * @param Exception $exception
     *
     * @return int
     */
    public function getStatusCode(Exception $exception): int
    {
        if ($exception instanceof AccessDeniedHttpException) {
            return Response::HTTP_UNAUTHORIZED;
        } elseif ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     *
     * @param Exception $exception
     *
     * @return string
     */
    public function getStatusText(Exception $exception): string
    {
        $code = $this->getStatusCode($exception);
        if ($code == Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        } else {
            $isCodeExists = array_key_exists($code, Response::$statusTexts);
            return ($isCodeExists) ? Response::$statusTexts[$code]
                : Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }
}
