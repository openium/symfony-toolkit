<?php

namespace Openium\SymfonyToolKitBundle\Tests\EventListener;

use LogicException;
use Openium\SymfonyToolKitBundle\Service\ExceptionFormatServiceInterface;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\EventListener\TestPathKernelExceptionListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
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

    protected function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testIsEnable(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $listener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            true,
            $this->logger
        );
        self::assertTrue($listener->getEnable());
        $listener = new TestPathKernelExceptionListener(
            $exceptionFormat,
            '/api',
            false,
            $this->logger
        );
        self::assertFalse($listener->getEnable());
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function statusCodeToLogLevelProvider(): iterable
    {
        yield '500 is critical' => [Response::HTTP_INTERNAL_SERVER_ERROR, LogLevel::CRITICAL];
        yield '503 is critical' => [Response::HTTP_SERVICE_UNAVAILABLE, LogLevel::CRITICAL];
        yield '400 is a warning' => [Response::HTTP_BAD_REQUEST, LogLevel::WARNING];
        yield '401 is a warning' => [Response::HTTP_UNAUTHORIZED, LogLevel::WARNING];
        yield '403 is a warning' => [Response::HTTP_FORBIDDEN, LogLevel::WARNING];
        yield '404 is a warning' => [Response::HTTP_NOT_FOUND, LogLevel::WARNING];
        yield '422 is a warning' => [Response::HTTP_UNPROCESSABLE_ENTITY, LogLevel::WARNING];
    }

    #[DataProvider('statusCodeToLogLevelProvider')]
    public function testOnKernelExceptionLogsTheExceptionInContextThenSetsTheResponse(
        int $statusCode,
        string $expectedLevel
    ): void {
        $exception = new LogicException('Something went wrong');
        $response = new Response('test', $statusCode);
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $exceptionFormat->expects(self::once())
            ->method('formatExceptionResponse')
            ->with($exception)
            ->willReturn($response);
        $this->logger->expects(self::once())
            ->method('log')
            ->with(
                $expectedLevel,
                sprintf(
                    'Uncaught PHP Exception LogicException: "Something went wrong" at %s line %d',
                    basename(__FILE__),
                    $exception->getLine()
                ),
                ['exception' => $exception]
            );
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/api/users'),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        $listener->onKernelException($event);

        self::assertSame($response, $event->getResponse());
    }

    public function testOnKernelExceptionIgnoresRequestsOutsideThePath(): void
    {
        $exceptionFormat = $this->createMock(ExceptionFormatServiceInterface::class);
        $exceptionFormat->expects(self::never())->method('formatExceptionResponse');
        $this->logger->expects(self::never())->method('log');
        $listener = new TestPathKernelExceptionListener($exceptionFormat, '/api', true, $this->logger);
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/admin'),
            HttpKernelInterface::MAIN_REQUEST,
            new LogicException('Something went wrong')
        );

        $listener->onKernelException($event);

        self::assertNull($event->getResponse());
    }
}
