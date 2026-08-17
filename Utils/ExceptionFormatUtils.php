<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExceptionFormatUtils implements ExceptionFormatUtilsInterface
{
    /**
     * getStatusCode
     */
    public function getStatusCode(Exception $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof AuthenticationException) {
            return Response::HTTP_UNAUTHORIZED;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     */
    public function getStatusText(Exception $exception): string
    {
        $code = $this->getStatusCode($exception);
        if ($code === Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        }
        $isCodeExists = array_key_exists($code, Response::$statusTexts);
        return ($isCodeExists)
            ? Response::$statusTexts[$code]
            : Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
    }
}
