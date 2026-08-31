<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Doctrine\DBAL\Driver\Exception as DriverExceptionInterface;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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

    public function testToHttpExceptionHandlesSubclassOfKnownException(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $driverStub = new class extends \Exception implements DriverExceptionInterface {
            #[\Override]
            public function getSQLState(): ?string
            {
                return null;
            }
        };
        // a subclass of TableNotFoundException that switch($throwable::class) could never match
        $throwable = new class ($driverStub, null) extends TableNotFoundException {
        };
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Missing database table');
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
    }

    public function testToHttpExceptionHandlesGenericDriverExceptionAsBadRequest(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $driverStub = new class extends \Exception implements DriverExceptionInterface {
            #[\Override]
            public function getSQLState(): ?string
            {
                return null;
            }
        };
        // a plain DriverException, matching none of the more specific subclasses
        $throwable = new DriverException($driverStub, null);
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Database schema error');
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
    }

    public function testToHttpExceptionHandlesGenericDbalExceptionViaSqlState(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        // a Doctrine\DBAL\Exception implementor that does not extend DriverException:
        // switch($throwable::class) could never match Exception::class since it's an interface.
        $throwable = new class ('conflict', 23000) extends \Exception implements DBALException {
        };
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService($logger);
        // then
        self::expectException(ConflictHttpException::class);
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
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
