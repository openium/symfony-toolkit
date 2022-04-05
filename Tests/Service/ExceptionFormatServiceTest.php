<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 *
 * @codeCoverageIgnore
 */
class ExceptionFormatServiceTest extends TestCase
{
    private $testKernel;

    public function setUp(): void
    {
        $this->testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testGetStatusCodeWithHttpException()
    {
        $exception = new HttpException(Response::HTTP_FORBIDDEN);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        self::assertTrue(is_int($statusCode));
        self::assertEquals($statusCode, Response::HTTP_FORBIDDEN);
    }

    public function testGetStatusCodeWithAccessDeniedException()
    {
        $exception = new \Exception();
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        self::assertTrue(is_int($statusCode));
        self::assertEquals($statusCode, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetStatusTextWith402HttpException()
    {
        $exception = new HttpException(Response::HTTP_PAYMENT_REQUIRED);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        self::assertTrue(is_string($text));
        self::assertEquals($text, 'Request Failed');
    }

    public function testGetStatusTextWith404HttpException()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        self::assertTrue(is_string($text));
        self::assertEquals($text, Response::$statusTexts[Response::HTTP_NOT_FOUND]);
    }

    public function testGetStatusTextWithUnknowHttpException()
    {
        $exception = new HttpException(456987);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        self::assertTrue(is_string($text));
        self::assertEquals($text, Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
    }

    public function testGetArrayWith404HttpException()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $this->testKernel->expects(self::once())
            ->method('getEnvironment')
            ->will($this->returnValue("prod"));
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($exception);
        self::assertTrue(is_array($result));
        self::assertTrue(array_key_exists('status_code', $result));
        self::assertEquals($result['status_code'], 404);
        self::assertTrue(array_key_exists('status_text', $result));
        self::assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        self::assertTrue(array_key_exists('message', $result));
        self::assertFalse(array_key_exists('trace', $result));
        self::assertFalse(array_key_exists('previous', $result));
    }

    public function testGetArrayWith404HttpExceptionAndDevEnd()
    {
        $previous = new \Exception('previous exception', 123456, null);
        $this->testKernel->expects(self::once())
            ->method('getEnvironment')
            ->will($this->returnValue("dev"));
        $exception = new HttpException(Response::HTTP_NOT_FOUND, 'message exception', $previous);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($exception);
        self::assertTrue(is_array($result));
        self::assertTrue(array_key_exists('status_code', $result));
        self::assertEquals($result['status_code'], 404);
        self::assertTrue(array_key_exists('status_text', $result));
        self::assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        self::assertTrue(array_key_exists('message', $result));
        self::assertEquals('message exception', $result['message']);
        self::assertTrue(array_key_exists('trace', $result));
        self::assertTrue(array_key_exists('previous', $result));
        self::assertEquals('previous exception', $result['previous']['message']);
        self::assertEquals(123456, $result['previous']['code']);
    }

    public function testFormatExceptionResponse()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $this->testKernel->expects(self::once())
            ->method('getEnvironment')
            ->will($this->returnValue("prod"));
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        self::assertTrue($response instanceof Response);
        self::assertEquals($response->getStatusCode(), Response::HTTP_NOT_FOUND);
    }


    public function testFormatExceptionResponseWithTypeError()
    {
        $errorMsg = 'Type error';
        $exception = new \TypeError($errorMsg);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        self::assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        self::assertTrue($response instanceof Response);
        self::assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertEquals($errorMsg, $response->getContent());
    }
}
