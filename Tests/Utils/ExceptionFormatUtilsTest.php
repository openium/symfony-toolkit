<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use Exception;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ExceptionFormatUtilsTest extends TestCase
{
    public function testGetStatusCodeReturnsStatusCodeForHttpException(): void
    {
        $httpException = new HttpException(Response::HTTP_NOT_FOUND, 'Not Found');
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals(404, $exceptionFormatUtils->getStatusCode($httpException));
    }

    public function testGetStatusCodeReturnsInternalServerErrorForGenericException(): void
    {
        $exception = new Exception('Erreur');
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $exceptionFormatUtils->getStatusCode($exception)
        );
    }

    public function testGetStatusTextReturnsRequestFailedForPaymentRequired(): void
    {
        $httpException = new HttpException(Response::HTTP_PAYMENT_REQUIRED);
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals('Request Failed', $exceptionFormatUtils->getStatusText($httpException));
    }

    public function testGetStatusTextReturnsStatusTextForKnownCode(): void
    {
        $httpException = new HttpException(Response::HTTP_FORBIDDEN);
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals('Forbidden', $exceptionFormatUtils->getStatusText($httpException));
    }

    public function testGetStatusTextReturnsInternalServerErrorForUnknownCode(): void
    {
        $httpException = new HttpException(599);
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals('Internal Server Error', $exceptionFormatUtils->getStatusText($httpException));
    }

    public function testGetStatusCodeReturnsUnauthorizedForBadCredentialsException(): void
    {
        $badCredentialsException = new BadCredentialsException();
        $exceptionFormatUtils = new ExceptionFormatUtils();
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $exceptionFormatUtils->getStatusCode($badCredentialsException));
    }
}
