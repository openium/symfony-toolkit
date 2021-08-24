<?php

namespace Openium\SymfonyToolKitBundle\Test\Service;

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
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        $this->assertTrue(is_int($statusCode));
        $this->assertEquals($statusCode, Response::HTTP_FORBIDDEN);
    }

    public function testGetStatusCodeWithAccessDeniedException()
    {
        $exception = new \Exception();
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $statusCode = $exceptionFormatService->getStatusCode($exception);
        $this->assertTrue(is_int($statusCode));
        $this->assertEquals($statusCode, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testGetStatusTextWith402HttpException()
    {
        $exception = new HttpException(Response::HTTP_PAYMENT_REQUIRED);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        $this->assertTrue(is_string($text));
        $this->assertEquals($text, 'Request Failed');
    }

    public function testGetStatusTextWith404HttpException()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        $this->assertTrue(is_string($text));
        $this->assertEquals($text, Response::$statusTexts[Response::HTTP_NOT_FOUND]);
    }

    public function testGetStatusTextWithUnknowHttpException()
    {
        $exception = new HttpException(456987);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $text = $exceptionFormatService->getStatusText($exception);
        $this->assertTrue(is_string($text));
        $this->assertEquals($text, Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
    }

    public function testGetArrayWith404HttpException()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $this->testKernel->expects($this->once())
            ->method('getEnvironment')
            ->will($this->returnValue("prod"));
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($exception);
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('status_code', $result));
        $this->assertEquals($result['status_code'], 404);
        $this->assertTrue(array_key_exists('status_text', $result));
        $this->assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertFalse(array_key_exists('trace', $result));
        $this->assertFalse(array_key_exists('previous', $result));
    }

    public function testGetArrayWith404HttpExceptionAndDevEnd()
    {
        $previous = new \Exception('previous exception', 123456, null);
        $this->testKernel->expects($this->once())
            ->method('getEnvironment')
            ->will($this->returnValue("dev"));
        $exception = new HttpException(Response::HTTP_NOT_FOUND, 'message exception', $previous);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $result = $exceptionFormatService->getArray($exception);
        $this->assertTrue(is_array($result));
        $this->assertTrue(array_key_exists('status_code', $result));
        $this->assertEquals($result['status_code'], 404);
        $this->assertTrue(array_key_exists('status_text', $result));
        $this->assertEquals($result['status_text'], Response::$statusTexts[Response::HTTP_NOT_FOUND]);
        $this->assertTrue(array_key_exists('message', $result));
        $this->assertEquals('message exception', $result['message']);
        $this->assertTrue(array_key_exists('trace', $result));
        $this->assertTrue(array_key_exists('previous', $result));
        $this->assertEquals('previous exception', $result['previous']['message']);
        $this->assertEquals(123456, $result['previous']['code']);
    }

    public function testFormatExceptionResponse()
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $this->testKernel->expects($this->once())
            ->method('getEnvironment')
            ->will($this->returnValue("prod"));
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        $this->assertTrue($response instanceof Response);
        $this->assertEquals($response->getStatusCode(), Response::HTTP_NOT_FOUND);
    }


    public function testFormatExceptionResponseWithTypeError()
    {
        $errorMsg = 'Type error';
        $exception = new \TypeError($errorMsg);
        $exceptionFormatService = new ExceptionFormatService($this->testKernel);
        $this->assertTrue($exceptionFormatService instanceof ExceptionFormatService);
        $response = $exceptionFormatService->formatExceptionResponse($exception);
        $this->assertTrue($response instanceof Response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertEquals($errorMsg, $response->getContent());
    }
}
