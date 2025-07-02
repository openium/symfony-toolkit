<?php

namespace Openium\SymfonyToolKitBundle\Tests\Utils;

use Exception;
use Openium\SymfonyToolKitBundle\Utils\ExceptionFormatUtils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionFormatUtilsTest extends TestCase
{
    public function testGetStatusCodeReturnsStatusCodeForHttpException(): void
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND, 'Not Found');
        $utils = new ExceptionFormatUtils();
        self::assertEquals(404, $utils->getStatusCode($exception));
    }

    public function testGetStatusCodeReturnsInternalServerErrorForGenericException(): void
    {
        $exception = new Exception('Erreur');
        $utils = new ExceptionFormatUtils();
        self::assertEquals(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $utils->getStatusCode($exception)
        );
    }

    public function testGetStatusTextReturnsRequestFailedForPaymentRequired(): void
    {
        $exception = new HttpException(Response::HTTP_PAYMENT_REQUIRED);
        $utils = new ExceptionFormatUtils();
        self::assertEquals('Request Failed', $utils->getStatusText($exception));
    }

    public function testGetStatusTextReturnsStatusTextForKnownCode(): void
    {
        $exception = new HttpException(Response::HTTP_FORBIDDEN);
        $utils = new ExceptionFormatUtils();
        self::assertEquals('Forbidden', $utils->getStatusText($exception));
    }

    public function testGetStatusTextReturnsInternalServerErrorForUnknownCode(): void
    {
        $exception = new HttpException(599);
        $utils = new ExceptionFormatUtils();
        self::assertEquals('Internal Server Error', $utils->getStatusText($exception));
    }
}
