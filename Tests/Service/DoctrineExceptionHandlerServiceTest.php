<?php

namespace Openium\SymfonyToolKitBundle\Test\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use Openium\SymfonyToolKitBundle\Tests\Fixtures\Exception\FakeException;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
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

    public function setUp()
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

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\ConflictHttpException
     * @expectedExceptionMessage Conflict error
     */
    public function testToHttpExceptionWithUniqueConstraintViolationException()
    {
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $ucve = new UniqueConstraintViolationException("message", $exceptionDE);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ucve);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Entity's management error
     */
    public function testToHttpExceptionWithNotNullConstraintViolationException()
    {
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $nncve = new NotNullConstraintViolationException("message", $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($nncve);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Entity's management error
     */
    public function testToHttpExceptionWithORMInvalidArgumentException()
    {
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $ormiae = new ORMInvalidArgumentException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($ormiae);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Entity's management error
     */
    public function testToHttpExceptionWithUnexpectedValueException()
    {
        $exc = new \PDOException();
        $pdoExc = new PDOException($exc);
        $exceptionDE = new PDOException($pdoExc);
        $uve = new \UnexpectedValueException("message", 0, $exceptionDE);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($uve);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\ConflictHttpException
     * @expectedExceptionMessage Conflict error
     */
    public function testToHttpExceptionWith23000DBALException()
    {
        $exception = new \Exception("test", 23000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(6))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Database error
     */
    public function testToHttpExceptionWith42000DBALException()
    {
        $exception = new \Exception("test", 42000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Database request error
     */
    public function testToHttpExceptionWith21000DBALException()
    {
        $exception = new \Exception("test", 21000);
        $dbal = new DBALException("message", 0, $exception);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Database request error
     */
    public function testToHttpExceptionWith21000DBALExceptionAndWithoutPreviousException()
    {
        $dbal = new DBALException("message", 21000, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Entity's management error
     */
    public function testToHttpExceptionWith0DBALExceptionAndWithoutPreviousException()
    {
        $dbal = new DBALException("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }

    public function testToHttpExceptionWithException()
    {
        $dbal = new \Exception("message", 0, null);
        $this->logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandler = new DoctrineExceptionHandlerService($this->logger);
        $this->assertTrue($doctrineExceptionHandler instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandler->toHttpException($dbal);
    }
}
