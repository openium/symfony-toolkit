<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\ServerService;
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
    private $requestStack;

    public function setUp(): void
    {
        $this->requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testGetBasePathWithRequest()
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
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'https://127.0.0.2/');
    }

    public function testGetBasePathWithRequestNotSecure()
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
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, 'http://127.0.0.2/');
    }

    public function testGetBasePathWithoutRequest()
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
