<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class DoctrineExceptionHandlerServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[CoversNothing]
class DoctrineExceptionHandlerServiceTest extends TestCase
{
    public function testLog(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $throwable = $this->createStub(\Exception::class);
        $logger->expects($this->exactly(5))->method('error');
        // TODO correct test
        //$throwable->expects(self::once())->method('getMessage')->will($this->returnValue("test"));
        //$throwable->expects(self::once())->method('getTraceAsString')->will($this->returnValue("test"));
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        self::assertTrue($doctrineExceptionHandlerService instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandlerService->log($throwable);
    }

    public function testToHttpExceptionWithException(): never
    {
        static::expectException("Exception");
        static::expectExceptionMessage("message");
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $exception = new \Exception("message", 0, null);
        $logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        self::assertTrue($doctrineExceptionHandlerService instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandlerService->toHttpException($exception);
    }

    public function testMissingDatabaseTableMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Missing database table",
            $doctrineExceptionHandlerService->getMissingDatabaseTableMessage()
        );
        // when
        $doctrineExceptionHandlerService->setMissingDatabaseTableMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getMissingDatabaseTableMessage());
    }

    public function testDatabaseSchemaErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Database schema error",
            $doctrineExceptionHandlerService->getDatabaseSchemaErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setDatabaseSchemaErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getDatabaseSchemaErrorMessage());
    }

    public function testQuerySyntaxErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Query syntax error",
            $doctrineExceptionHandlerService->getQuerySyntaxErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setQuerySyntaxErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getQuerySyntaxErrorMessage());
    }

    public function testEntityManagementErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Entity's management error",
            $doctrineExceptionHandlerService->getEntityManagementErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setEntityManagementErrorMessage($message);
        // then
        static::assertEquals(
            $message,
            $doctrineExceptionHandlerService->getEntityManagementErrorMessage()
        );
    }

    public function testConflictMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals("Conflict error", $doctrineExceptionHandlerService->getConflictMessage());
        // when
        $doctrineExceptionHandlerService->setConflictMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getConflictMessage());
    }

    public function testDatabaseErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Database error",
            $doctrineExceptionHandlerService->getDatabaseErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setDatabaseErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getDatabaseErrorMessage());
    }

    public function testDatabaseRequestErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Database request error",
            $doctrineExceptionHandlerService->getDatabaseRequestErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setDatabaseRequestErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getDatabaseRequestErrorMessage());
    }

    public function testMissingPropertyErrorMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        static::assertEquals(
            "Database schema error (Missing property)",
            $doctrineExceptionHandlerService->getMissingPropertyErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setMissingPropertyErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getMissingPropertyErrorMessage());
    }
}
