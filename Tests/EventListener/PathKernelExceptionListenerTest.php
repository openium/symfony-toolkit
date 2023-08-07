<?php
/**
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

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
    private MockObject&LoggerInterface $logger;

    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testIsEnable(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        self::assertTrue($listener->getEnable());
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', false, $this->logger);
        self::assertFalse($listener->getEnable());
    }

    public function testOnKernelExceptionWithRandowError(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test');
        $exceptionFormat->expects(self::once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects(self::once())->method('error');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new Exception("testError", 123);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        self::assertEquals($response, $event->getResponse());
    }

    public function testOnKernelExceptionWithCritError(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', Response::HTTP_INTERNAL_SERVER_ERROR);
        $exceptionFormat->expects(self::once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects(self::once())->method('critical');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new Exception("testError", Response::HTTP_INTERNAL_SERVER_ERROR);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        self::assertEquals($response, $event->getResponse());
    }

    public function testOnKernelExceptionWithAuthError(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', Response::HTTP_UNAUTHORIZED);
        $exceptionFormat->expects(self::once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects(self::once())->method('info');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new Exception("testError", Response::HTTP_UNAUTHORIZED);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        self::assertEquals($response, $event->getResponse());
    }
}
