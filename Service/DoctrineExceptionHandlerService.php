<?php

/**
 * DoctrineExceptionHandlerService
 *
 * PHP Version 7.1, 7.2
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\NonUniqueFieldNameException;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\DBAL\Exception\SyntaxErrorException;
use Doctrine\DBAL\Exception\TableExistsException;
use Doctrine\DBAL\Exception\TableNotFoundException;
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
    private $missingDatabaseTableMessage = "Missing database table";
    private $databaseSchemaErrorMessage = "Database schema error";
    private $querySyntaxErrorMessage = "Query syntax error";
    private $entityManagementErrorMessage = "Entity's management error";
    private $conflictMessage = "Conflict error";
    private $databaseErrorMessage = "Database error";
    private $databaseRequestErrorMessage = "Database request error";
    private $missingPropertyErrorMessage = "Database schema error (Missing property)";

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
     * toHttpException
     * Catch & Process the throwable
     *
     * @param \Throwable $throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     * @throws \Throwable if not a doctrine exception
     */
    public function toHttpException(\Throwable $throwable)
    {
        // Call the logger
        $this->log($throwable);
        // Select the process
        switch (get_class($throwable)) {
            case TableNotFoundException::class:
                $this->createBadRequest($throwable, $this->missingDatabaseTableMessage);
                break;
            case DriverException::class:
            case TableExistsException::class:
            case NonUniqueFieldNameException::class:
                $this->createBadRequest($throwable, $this->databaseSchemaErrorMessage);
                break;
            case SyntaxErrorException::class:
                $this->createBadRequest($throwable, $this->querySyntaxErrorMessage);
                break;
            case UniqueConstraintViolationException::class:
            case ForeignKeyConstraintViolationException::class:
                $this->createConflict($throwable, $this->conflictMessage);
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
                throw $throwable;
        }
    }

    /**
     * createBadRequest
     *
     * @param \Throwable $throwable
     * @param string|null $message
     *
     * @return void
     * @throws BadRequestHttpException
     *
     */
    protected function createBadRequest(\Throwable $throwable, string $message = null)
    {
        throw new BadRequestHttpException($message ?? $this->entityManagementErrorMessage, $throwable);
    }

    /**
     * createConflict
     *
     * @param \Throwable $throwable
     * @param null $message
     *
     * @return void
     * @throws ConflictHttpException
     *
     */
    protected function createConflict(\Throwable $throwable, $message = null)
    {
        if ($throwable->getPrevious()) {
            $this->logger->error($throwable->getPrevious()->getCode());
        }
        throw new ConflictHttpException($message ?? $this->conflictMessage, $throwable);
    }

    /**
     * dbalManagement
     *
     * @param DBALException $DBALException
     *
     * @return void
     * @throws ConflictHttpException
     *
     * @throws BadRequestHttpException
     */
    protected function dbalManagement(DBALException $DBALException)
    {
        $code = 0;
        $previous = $DBALException->getPrevious();
        if ($previous) {
            $code = $previous->getCode() ?? 0;
        } elseif ($DBALException->getCode()) {
            $code = $DBALException->getCode();
        }
        switch ($code) {
            case '23000':
                $this->createConflict($DBALException);
                break;
            case '42000':
                $this->createBadRequest($DBALException, $this->databaseErrorMessage);
                break;
            case '21000':
                $this->createBadRequest($DBALException, $this->databaseRequestErrorMessage);
                break;
            case '21S01':
                $this->createBadRequest($DBALException, $this->missingPropertyErrorMessage);
                break;
            case '42S02':
                $this->createBadRequest($DBALException, $this->missingDatabaseTableMessage);
                break;
            default:
                break;
        }
        $this->createBadRequest($DBALException);
    }

    /**
     * Getter for missingDatabaseTableMessage
     *
     * @return string
     */
    public function getMissingDatabaseTableMessage(): string
    {
        return $this->missingDatabaseTableMessage;
    }

    /**
     * Setter for missingDatabaseTableMessage
     *
     * @param string $missingDatabaseTableMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setMissingDatabaseTableMessage(string $missingDatabaseTableMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->missingDatabaseTableMessage = $missingDatabaseTableMessage;
        return $this;
    }

    /**
     * Getter for databaseSchemaErrorMessage
     *
     * @return string
     */
    public function getDatabaseSchemaErrorMessage(): string
    {
        return $this->databaseSchemaErrorMessage;
    }

    /**
     * Setter for databaseSchemaErrorMessage
     *
     * @param string $databaseSchemaErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setDatabaseSchemaErrorMessage(string $databaseSchemaErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->databaseSchemaErrorMessage = $databaseSchemaErrorMessage;
        return $this;
    }

    /**
     * Getter for querySyntaxErrorMessage
     *
     * @return string
     */
    public function getQuerySyntaxErrorMessage(): string
    {
        return $this->querySyntaxErrorMessage;
    }

    /**
     * Setter for querySyntaxErrorMessage
     *
     * @param string $querySyntaxErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setQuerySyntaxErrorMessage(string $querySyntaxErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->querySyntaxErrorMessage = $querySyntaxErrorMessage;
        return $this;
    }

    /**
     * Getter for entityManagementErrorMessage
     *
     * @return string
     */
    public function getEntityManagementErrorMessage(): string
    {
        return $this->entityManagementErrorMessage;
    }

    /**
     * Setter for entityManagementErrorMessage
     *
     * @param string $entityManagementErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setEntityManagementErrorMessage(string $entityManagementErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->entityManagementErrorMessage = $entityManagementErrorMessage;
        return $this;
    }

    /**
     * Getter for conflictMessage
     *
     * @return string
     */
    public function getConflictMessage(): string
    {
        return $this->conflictMessage;
    }

    /**
     * Setter for conflictMessage
     *
     * @param string $conflictMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setConflictMessage(string $conflictMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->conflictMessage = $conflictMessage;
        return $this;
    }

    /**
     * Getter for databaseErrorMessage
     *
     * @return string
     */
    public function getDatabaseErrorMessage(): string
    {
        return $this->databaseErrorMessage;
    }

    /**
     * Setter for databaseErrorMessage
     *
     * @param string $databaseErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setDatabaseErrorMessage(string $databaseErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->databaseErrorMessage = $databaseErrorMessage;
        return $this;
    }

    /**
     * Getter for databaseRequestErrorMessage
     *
     * @return string
     */
    public function getDatabaseRequestErrorMessage(): string
    {
        return $this->databaseRequestErrorMessage;
    }

    /**
     * Setter for databaseRequestErrorMessage
     *
     * @param string $databaseRequestErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setDatabaseRequestErrorMessage(string $databaseRequestErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->databaseRequestErrorMessage = $databaseRequestErrorMessage;
        return $this;
    }

    /**
     * Getter for missingPropertyErrorMessage
     *
     * @return string
     */
    public function getMissingPropertyErrorMessage(): string
    {
        return $this->missingPropertyErrorMessage;
    }

    /**
     * Setter for missingPropertyErrorMessage
     *
     * @param string $missingPropertyErrorMessage
     *
     * @return DoctrineExceptionHandlerServiceInterface
     */
    public function setMissingPropertyErrorMessage(string $missingPropertyErrorMessage): DoctrineExceptionHandlerServiceInterface
    {
        $this->missingPropertyErrorMessage = $missingPropertyErrorMessage;
        return $this;
    }
}
