<?php

/**
 * PHP Version >=7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\EventListener;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener\TestPathKernelExceptionListener;
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
    private $logger;

    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testIsEnable()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $this->assertTrue($listener->getEnable());
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', false, $this->logger);
        $this->assertFalse($listener->getEnable());
    }

    public function testOnKernelExceptionWithRandowError()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test');
        $exceptionFormat->expects($this->once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects($this->once())->method('error');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new \Exception("testError", 123);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        $this->assertEquals($response, $event->getResponse());
    }

    public function testOnKernelExceptionWithCritError()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', 500);
        $exceptionFormat->expects($this->once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects($this->once())->method('critical');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new \Exception("testError", 500);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        $this->assertEquals($response, $event->getResponse());
    }

    public function testOnKernelExceptionWithAuthError()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test', 401);
        $exceptionFormat->expects($this->once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $this->logger->expects($this->once())->method('info');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new \Exception("testError", 401);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        $this->assertEquals($response, $event->getResponse());
    }
}
