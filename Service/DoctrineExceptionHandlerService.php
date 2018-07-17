<?php

/**
 * DoctrineExceptionHandlerService
 *
 * PHP Version 7.1
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Thomas LEDUC <thomaslmoi15@hotmail.fr>, Alexandre Caillot <a.caillot@openium.fr>
 * @link     https://openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use function Couchbase\defaultDecoder;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use \UnexpectedValueException;

/**
 * Class DoctrineExceptionHandlerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class DoctrineExceptionHandlerService implements DoctrineExceptionHandlerServiceInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ExceptionHandlerService constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log an exception information for debug
     *
     * @param \Throwable $throwable
     */
    public function log(\Throwable $throwable)
    {
        $this->logger->error('-------------------------------------');
        $this->logger->error(get_class($throwable));
        $this->logger->error($throwable->getMessage());
        $this->logger->error($throwable->getTraceAsString());
        $this->logger->error('-------------------------------------');
    }

    /**
     * Catch & Process the throwable
     *
     * @param \Throwable $throwable
     */
    public function toHttpException(\Throwable $throwable)
    {
        // Call the logger
        $this->log($throwable);
        // Select the process
        switch (get_class($throwable)) {
            case UniqueConstraintViolationException::class:
                $this->createConflict($throwable);
                break;
            case DBALException::class:
                $this->dbalManagement($throwable);
                break;
            case NotNullConstraintViolationException::class:
            case ORMInvalidArgumentException::class:
            case UnexpectedValueException::class:
                $this->createBadRequest($throwable);
                break;
            default:
                break;
        }
    }

    /**
     * @param \Throwable $throwable
     * @param string|null $message
     *
     * @throws BadRequestHttpException
     *
     * @return void
     */
    protected function createBadRequest(\Throwable $throwable, string $message = null)
    {
        throw new BadRequestHttpException($message ?? "Entity's management error", $throwable);
    }

    /**
     * @param \Throwable $throwable
     * @param null $message
     *
     * @return void
     */
    protected function createConflict(\Throwable $throwable, $message = null)
    {
        $this->logger->error($throwable->getPrevious()->getCode());
        throw new ConflictHttpException($message ?? "Conflict error", $throwable);
    }

    /**
     * @param \Throwable $throwable
     *
     * @return void
     */
    protected function dbalManagement(\Throwable $throwable)
    {
        $previous = $throwable->getPrevious();
        $code = $previous->getCode() ?? 0;
        switch ($code) {
            case '23000':
                $this->createConflict($throwable);
                break;
            case '42000':
                $this->createBadRequest($throwable, "Database error");
                break;
            case '21000':
                $this->createBadRequest($throwable, "Database request error");
                break;
            case '21S01':
                $this->createBadRequest($throwable, "Database schema error (Missing property)");
                break;
            case '42S02':
                $this->createBadRequest($throwable, "Missing database table");
                break;
            default:
                break;
        }
        $this->createBadRequest($throwable);
    }
}
