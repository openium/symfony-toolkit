<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\ServerService;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ServerServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 *
 * @codeCoverageIgnore
 */
class ServerServiceTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    public function setUp(): void
    {
        $this->requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testGetBasePathWithRequest(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.2'));
        $request->expects(self::once())
            ->method('isSecure')
            ->will($this->returnValue(true));
        $request->expects(self::once())
            ->method('getPort')
            ->will($this->returnValue(443));
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'https://127.0.0.2/');
    }

    public function testGetBasePathWithRequestNotSecure(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHost')
            ->will($this->returnValue('127.0.0.2'));
        $request->expects(self::once())
            ->method('isSecure')
            ->will($this->returnValue(false));
        $request->expects(self::once())
            ->method('getPort')
            ->will($this->returnValue(80));
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'http://127.0.0.2/');
    }

    public function testGetBasePathWithRequestNonDefaultPort(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHost')
            ->will($this->returnValue('localhost'));
        $request->expects(self::once())
            ->method('isSecure')
            ->will($this->returnValue(false));
        $request->expects(self::once())
            ->method('getPort')
            ->will($this->returnValue(8080));
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'http://localhost:8080/');
    }

    public function testGetBasePathWithRequestNonDefaultPortSecure(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getHost')
            ->will($this->returnValue('localhost'));
        $request->expects(self::once())
            ->method('isSecure')
            ->will($this->returnValue(true));
        $request->expects(self::once())
            ->method('getPort')
            ->will($this->returnValue(8443));
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'https://localhost:8443/');
    }

    public function testGetBasePathWithoutRequest(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue(null));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, '');
    }
}
