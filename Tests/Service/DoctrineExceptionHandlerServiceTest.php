<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class DoctrineExceptionHandlerServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 *
 * @codeCoverageIgnore
 */
class DoctrineExceptionHandlerServiceTest extends TestCase
{
    private $logger;

    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        parent::setUp();
    }

    public function testLog(): void
    {

        $throwable = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger->expects($this->exactly(5))->method('error');
        // TODO correct test
        //$throwable->expects(self::once())->method('getMessage')->will($this->returnValue("test"));
        //$throwable->expects(self::once())->method('getTraceAsString')->will($this->returnValue("test"));
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->log($throwable);
    }
/*
    public function testToHttpExceptionWithUniqueConstraintViolationException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\ConflictHttpException");
        static::expectExceptionMessage("Conflict error");
        $exc = new \PDOException();
        $pdoExc = $this->createMock(Exception\DriverException::class)
        ->expects(self::once())->method('getPrevious')->willReturn($exc);
        $exceptionDE = new Exception\DriverException($pdoExc);
        $ucve = new UniqueConstraintViolationException($exceptionDE, null);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ucve);
    }

    public function testToHttpExceptionWithNotNullConstraintViolationException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = $this->createMock(Exception\DriverException::class)
            ->expects(self::once())->method('getPrevious')->willReturn($exc);
        $exceptionDE = new Exception\DriverException($pdoExc, null);
        $nncve = new NotNullConstraintViolationException($exceptionDE, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($nncve);
    }
*/
    public function testToHttpExceptionWithORMInvalidArgumentException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = new Exception($exc);
        $exceptionDE = new Exception($pdoExc);
        $ormiae = new ORMInvalidArgumentException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ormiae);
    }

    public function testToHttpExceptionWithUnexpectedValueException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = new Exception($exc);
        $exceptionDE = new Exception($pdoExc);
        $uve = new \UnexpectedValueException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($uve);
    }

    public function testToHttpExceptionWith23000DBALException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\ConflictHttpException");
        static::expectExceptionMessage("Conflict error");
        $exception = new \Exception("test", 23000);
        $dbal = new Exception("message", 0, $exception);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith42000DBALException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database error");
        $exception = new \Exception("test", 42000);
        $dbal = new Exception("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith21000DBALException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database request error");
        $exception = new \Exception("test", 21000);
        $dbal = new Exception("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith21000DBALExceptionAndWithoutPreviousException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database request error");
        $dbal = new Exception("message", 21000, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith0DBALExceptionAndWithoutPreviousException(): void
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $dbal = new Exception("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWithException(): void
    {
        static::expectException("Exception");
        static::expectExceptionMessage("message");
        $dbal = new \Exception("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        self::assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testMissingDatabaseTableMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Missing database table", $doctrineExceptionHandler->getMissingDatabaseTableMessage());
        // when
        $doctrineExceptionHandler->setMissingDatabaseTableMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getMissingDatabaseTableMessage());
    }

    public function testDatabaseSchemaErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Database schema error", $doctrineExceptionHandler->getDatabaseSchemaErrorMessage());
        // when
        $doctrineExceptionHandler->setDatabaseSchemaErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getDatabaseSchemaErrorMessage());
    }

    public function testQuerySyntaxErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Query syntax error", $doctrineExceptionHandler->getQuerySyntaxErrorMessage());
        // when
        $doctrineExceptionHandler->setQuerySyntaxErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getQuerySyntaxErrorMessage());
    }

    public function testEntityManagementErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Entity's management error", $doctrineExceptionHandler->getEntityManagementErrorMessage());
        // when
        $doctrineExceptionHandler->setEntityManagementErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getEntityManagementErrorMessage());
    }

    public function testConflictMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Conflict error", $doctrineExceptionHandler->getConflictMessage());
        // when
        $doctrineExceptionHandler->setConflictMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getConflictMessage());
    }

    public function testDatabaseErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Database error", $doctrineExceptionHandler->getDatabaseErrorMessage());
        // when
        $doctrineExceptionHandler->setDatabaseErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getDatabaseErrorMessage());
    }

    public function testDatabaseRequestErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Database request error", $doctrineExceptionHandler->getDatabaseRequestErrorMessage());
        // when
        $doctrineExceptionHandler->setDatabaseRequestErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getDatabaseRequestErrorMessage());
    }

    public function testMissingPropertyErrorMessage(): void
    {
        // given
        $message = "new error message";
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        // then
        static::assertEquals("Database schema error (Missing property)", $doctrineExceptionHandler->getMissingPropertyErrorMessage());
        // when
        $doctrineExceptionHandler->setMissingPropertyErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandler->getMissingPropertyErrorMessage());
    }
}
