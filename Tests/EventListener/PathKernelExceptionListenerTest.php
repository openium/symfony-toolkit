<?php

namespace Openium\SymfonyToolKitBundle\Tests\EventListener;

use Exception;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener\TestPathKernelExceptionListener;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class PathKernelExceptionListenerTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\EventListener
 */
class PathKernelExceptionListenerTest extends TestCase
{
    public function testIsEnable(): void
    {
        $logger = $this->createStub(LoggerInterface::class);
        $exceptionFormat = $this->createStub(ExceptionFormatServiceInterface::class);
        $listener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            true,
            $logger
        );
        self::assertTrue($listener->getEnable());
        $listener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            false,
            $logger
        );
        self::assertFalse($listener->getEnable());
    }

    public function testOnKernelExceptionWithRandowError(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
        ->disableOriginalConstructor()
        ->getMock();
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test');
        $exceptionFormat->expects(self::once())
            ->method('formatExceptionResponse')
            ->willReturn($response);
        $logger->expects(self::once())->method('error');
        $testPathKernelExceptionListener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            true,
            $logger
        );
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exception = new Exception("testError", 123);
        $exceptionEvent = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
        $testPathKernelExceptionListener->onKernelException($exceptionEvent);
        self::assertEquals($response, $exceptionEvent->getResponse());
    }

    public function testOnKernelExceptionWithCritError(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', Response::HTTP_INTERNAL_SERVER_ERROR);
        $exceptionFormat->expects(self::once())
            ->method('formatExceptionResponse')
            ->willReturn($response);
        $logger->expects(self::once())->method('critical');
        $testPathKernelExceptionListener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            true,
            $logger
        );
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exception = new Exception("testError", Response::HTTP_INTERNAL_SERVER_ERROR);
        $exceptionEvent = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
        $testPathKernelExceptionListener->onKernelException($exceptionEvent);
        self::assertEquals($response, $exceptionEvent->getResponse());
    }

    public function testOnKernelExceptionWithAuthError(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', Response::HTTP_UNAUTHORIZED);
        $exceptionFormat->expects(self::once())->method('formatExceptionResponse')
            ->willReturn($response);
        $logger->expects(self::once())->method('info');
        $testPathKernelExceptionListener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            true,
            $logger
        );
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exception = new Exception("testError", Response::HTTP_UNAUTHORIZED);
        $exceptionEvent = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
        $testPathKernelExceptionListener->onKernelException($exceptionEvent);
        self::assertEquals($response, $exceptionEvent->getResponse());
    }
}
