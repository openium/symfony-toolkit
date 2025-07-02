<?php

namespace Openium\SymfonyToolKitBundle\Utils;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionFormatUtils implements ExceptionFormatUtilsInterface
{
    /**
     * getStatusCode
     */
    public function getStatusCode(Exception $exception): int
    {
        return ($exception instanceof HttpExceptionInterface)
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * getStatusText
     */
    public function getStatusText(Exception $exception): string
    {
        $code = $this->getStatusCode($exception);
        if ($code == Response::HTTP_PAYMENT_REQUIRED) {
            return 'Request Failed';
        } else {
            $isCodeExists = array_key_exists($code, Response::$statusTexts);
            return ($isCodeExists)
                ? Response::$statusTexts[$code]
                : Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        }
    }
}
