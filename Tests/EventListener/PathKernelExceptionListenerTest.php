<?php

/**
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Tests\EventListener
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Tests\EventListener;

use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener\TestPathKernelExceptionListener;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class PathKernelExceptionListenerTest
 *
 * @package Openium\SymfonyToolKitBundle\Tests\EventListener
 */
class PathKernelExceptionListenerTest extends TestCase
{
    public function testIsEnable()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true);
        $this->assertTrue($listener->getEnable());
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', false);
        $this->assertFalse($listener->getEnable());
    }

    public function testOnKernelException()
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $response = new Response('test');
        $exceptionFormat->expects($this->once())->method('formatExceptionResponse')->will(
            $this->returnValue($response)
        );
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/api');
        $exc = new \Exception("testError", 123);
        $event = new GetResponseForExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exc);
        $listener->onKernelException($event);
        $this->assertEquals($response, $event->getResponse());
    }
}
