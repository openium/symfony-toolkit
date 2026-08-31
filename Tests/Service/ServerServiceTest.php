<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\ServerService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ServerServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[CoversNothing]
class ServerServiceTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testGetBasePathDelegatesToGetSchemeAndHttpHost(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getSchemeAndHttpHost')
            ->willReturn('https://127.0.0.2');
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertEquals('https://127.0.0.2/', $result);
    }

    public function testGetBasePathDelegatesToGetSchemeAndHttpHostWithNonDefaultPort(): void
    {
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getSchemeAndHttpHost')
            ->willReturn('http://localhost:8080');
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn($request);
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertEquals('http://localhost:8080/', $result);
    }

    public function testGetBasePathWithoutRequest(): void
    {
        $this->requestStack->expects(self::once())
            ->method('getCurrentRequest')
            ->willReturn(null);
        $serverService = new ServerService($this->requestStack);
        $result = $serverService->getBasePath();
        self::assertNotNull($result);
        self::assertEquals($result, '');
    }

    public function testConstructorTriggersDeprecation(): void
    {
        // given
        $deprecations = [];
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecations): bool {
            $deprecations[] = $errstr;
            return true;
        }, \E_USER_DEPRECATED);

        // when
        new ServerService($this->createStub(RequestStack::class));
        restore_error_handler();

        // then
        self::assertCount(1, $deprecations);
        self::assertStringContainsString('ServerService" class is deprecated', $deprecations[0]);
        self::assertStringContainsString('getSchemeAndHttpHost', $deprecations[0]);
    }
}
