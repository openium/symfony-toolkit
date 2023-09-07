<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Exception;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use TypeError;

/**
 * Class ExceptionFormatServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 *
 * @codeCoverageIgnore
 */
class ExceptionFormatExtendServiceTest extends TestCase
{    
    private MockObject&KernelInterface $testKernel;

    public function setUp(): void
    {
        $this->testKernel = $this->getMockBuilder(KernelInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testUserAuthenticatorCustom(): void
    {
        $exception = new HttpException(Response::HTTP_FORBIDDEN);
        $exceptionFormatExtendService = new ExceptionFormatExtendService($this->testKernel);
        [$code, $text, $message] = $exceptionFormatExtendService->genericExceptionResponse($exception);
        self::assertEquals(400, $code);
        self::assertEquals('bad request', $text);
        self::assertEquals('bad request', $message);
    }

    public function testFormatExceptionResponse(): void
    {
        $exception = new HttpException(Response::HTTP_NOT_FOUND);
        $this->testKernel->expects(self::once())
            ->method('getEnvironment')
            ->will($this->returnValue("prod"));
        $exceptionFormatExtendService = new ExceptionFormatExtendService($this->testKernel);
        self::assertTrue($exceptionFormatExtendService instanceof ExceptionFormatService);
        $response = $exceptionFormatExtendService->formatExceptionResponse($exception);
        self::assertTrue($response instanceof Response);
        self::assertEquals($response->getStatusCode(), Response::HTTP_BAD_REQUEST);
    }

}