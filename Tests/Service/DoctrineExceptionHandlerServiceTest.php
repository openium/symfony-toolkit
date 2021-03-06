<?php

namespace Openium\SymfonyToolKitBundle\Test\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Exception\FakeException;
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

    public function testLog()
    {

        $throwable = $this->getMockBuilder(\Exception::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->logger->expects($this->exactly(5))->method('error');
        // TODO correct test
        //$throwable->expects($this->once())->method('getMessage')->will($this->returnValue("test"));
        //$throwable->expects($this->once())->method('getTraceAsString')->will($this->returnValue("test"));
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->log($throwable);
    }

    public function testToHttpExceptionWithUniqueConstraintViolationException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\ConflictHttpException");
        static::expectExceptionMessage("Conflict error");
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $ucve = new UniqueConstraintViolationException("message", $exceptionDE);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ucve);
    }

    public function testToHttpExceptionWithNotNullConstraintViolationException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $nncve = new NotNullConstraintViolationException("message", $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($nncve);
    }

    public function testToHttpExceptionWithORMInvalidArgumentException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $ormiae = new ORMInvalidArgumentException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ormiae);
    }

    public function testToHttpExceptionWithUnexpectedValueException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $uve = new \UnexpectedValueException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($uve);
    }

    public function testToHttpExceptionWith23000DBALException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\ConflictHttpException");
        static::expectExceptionMessage("Conflict error");
        $exception = new \Exception("test", 23000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith42000DBALException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database error");
        $exception = new \Exception("test", 42000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith21000DBALException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database request error");
        $exception = new \Exception("test", 21000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith21000DBALExceptionAndWithoutPreviousException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Database request error");
        $dbal = new DBALException("message", 21000, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWith0DBALExceptionAndWithoutPreviousException()
    {
        static::expectException("Symfony\Component\HttpKernel\Exception\BadRequestHttpException");
        static::expectExceptionMessage("Entity's management error");
        $dbal = new DBALException("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWithException()
    {
        static::expectException("Exception");
        static::expectExceptionMessage("message");
        $dbal = new \Exception("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testMissingDatabaseTableMessage()
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

    public function testDatabaseSchemaErrorMessage()
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

    public function testQuerySyntaxErrorMessage()
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

    public function testEntityManagementErrorMessage()
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

    public function testConflictMessage()
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

    public function testDatabaseErrorMessage()
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

    public function testDatabaseRequestErrorMessage()
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

    public function testMissingPropertyErrorMessage()
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
