<?php

/**
 * DoctrineExceptionHandlerService
 *
 * PHP Version >=8.0
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 * @author   Openium <contact@openium.fr>
 * @license  Openium All right reserved
 * @link     https://www.openium.fr/
 */

namespace Openium\SymfonyToolKitBundle\Service;

use Doctrine\DBAL\Exception;
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
use Throwable;
use UnexpectedValueException;

/**
 * Class DoctrineExceptionHandlerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class DoctrineExceptionHandlerService implements DoctrineExceptionHandlerServiceInterface
{
    private string $missingDatabaseTableMessage = "Missing database table";
    private string $databaseSchemaErrorMessage = "Database schema error";
    private string $querySyntaxErrorMessage = "Query syntax error";
    private string $entityManagementErrorMessage = "Entity's management error";
    private string $conflictMessage = "Conflict error";
    private string $databaseErrorMessage = "Database error";
    private string $databaseRequestErrorMessage = "Database request error";
    private string $missingPropertyErrorMessage = "Database schema error (Missing property)";

    protected LoggerInterface $logger;

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
     * @param Throwable $throwable
     */
    public function log(Throwable $throwable): void
    {
        $this->logger->error('------------------------------------- Log from Symfony ToolKit DoctrineExceptionHandlerService');
        $this->logger->error(get_class($throwable));
        $this->logger->error($throwable->getMessage());
        $this->logger->error($throwable->getTraceAsString());
        $this->logger->error('-------------------------------------');
    }

    /**
     * toHttpException
     * Catch & Process the throwable
     *
     * @param Throwable $throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     * @throws Throwable if not a doctrine exception
     * TODO change return type to never when upgrade to php 8.1
     */
    public function toHttpException(Throwable $throwable): void
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
            case NotNullConstraintViolationException::class:
            case ORMInvalidArgumentException::class:
            case UnexpectedValueException::class:
                $this->createBadRequest($throwable);
                break;
            case Exception::class:
                $this->dbalExceptionManagement($throwable);
                break;
            default:
                throw $throwable;
        }
    }

    /**
     * createBadRequest
     *
     * @param Throwable $throwable
     * @param string|null $message
     *
     * @return void
     * @throws BadRequestHttpException
     * TODO change return type to never when upgrade to php 8.1
     */
    protected function createBadRequest(Throwable $throwable, string $message = null): void
    {
        throw new BadRequestHttpException($message ?? $this->entityManagementErrorMessage, $throwable);
    }

    /**
     * createConflict
     *
     * @param Throwable $throwable
     * @param string|null $message
     *
     * @return void
     * @throws ConflictHttpException
     * TODO change return type to never when upgrade to php 8.1
     */
    protected function createConflict(Throwable $throwable, ?string $message = null): void
    {
        if ($throwable->getPrevious() !== null) {
            $this->logger->error($throwable->getPrevious()->getCode());
        }
        throw new ConflictHttpException($message ?? $this->conflictMessage, $throwable);
    }

    /**
     * dbalExceptionManagement
     *
     * @param Exception $DBALException
     *
     * @return void
     * @throws ConflictHttpException
     *
     * @throws BadRequestHttpException
     * TODO change return type to never when upgrade to php 8.1
     */
    protected function dbalExceptionManagement(Exception $DBALException): void
    {
        $code = '0';
        $previous = $DBALException->getPrevious();
        if ($previous !== null) {
            $code = strval($previous->getCode());
        } else {
            $code = strval($DBALException->getCode());
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
