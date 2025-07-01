<?php

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

    /**
     * ExceptionHandlerService constructor.
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Log an exception information for debug
     */
    public function log(Throwable $throwable): void
    {
        $this->logger->error(
            '------------------------------------- Log from Symfony ToolKit DoctrineExceptionHandlerService'
        );
        $this->logger->error($throwable::class);
        $this->logger->error($throwable->getMessage());
        $this->logger->error($throwable->getTraceAsString());
        $this->logger->error('-------------------------------------');
    }

    /**
     * toHttpException
     * Catch & Process the throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     * @throws Throwable if not a doctrine exception
     */
    public function toHttpException(Throwable $throwable): never
    {
        // Call the logger
        $this->log($throwable);
        // Select the process
        switch ($throwable::class) {
            case TableNotFoundException::class:
                $this->createBadRequest($throwable, $this->missingDatabaseTableMessage);
            case DriverException::class:
            case TableExistsException::class:
            case NonUniqueFieldNameException::class:
                $this->createBadRequest($throwable, $this->databaseSchemaErrorMessage);
            case SyntaxErrorException::class:
                $this->createBadRequest($throwable, $this->querySyntaxErrorMessage);
            case UniqueConstraintViolationException::class:
            case ForeignKeyConstraintViolationException::class:
                $this->createConflict($throwable, $this->conflictMessage);
            case NotNullConstraintViolationException::class:
            case ORMInvalidArgumentException::class:
            case UnexpectedValueException::class:
                $this->createBadRequest($throwable);
            case Exception::class:
                $this->dbalExceptionManagement($throwable);
            default:
                throw $throwable;
        }
    }

    /**
     * createBadRequest
     *
     * @param string|null $message
     *
     * @throws BadRequestHttpException
     */
    protected function createBadRequest(Throwable $throwable, string $message = null): never
    {
        throw new BadRequestHttpException(
            $message ?? $this->entityManagementErrorMessage,
            $throwable
        );
    }

    /**
     * createConflict
     *
     *
     * @throws ConflictHttpException
     */
    protected function createConflict(Throwable $throwable, ?string $message = null): never
    {
        if ($throwable->getPrevious() instanceof \Throwable) {
            $this->logger->error($throwable->getPrevious()->getCode());
        }

        throw new ConflictHttpException($message ?? $this->conflictMessage, $throwable);
    }

    /**
     * dbalExceptionManagement
     *
     * @throws ConflictHttpException
     * @throws BadRequestHttpException
     */
    protected function dbalExceptionManagement(Exception $DBALException): never
    {
        $previous = $DBALException->getPrevious();
        $code = $previous instanceof \Throwable ? (string)$previous->getCode()
            : (string)$DBALException->getCode();
        switch ($code) {
            case '23000':
                $this->createConflict($DBALException);
            case '42000':
                $this->createBadRequest($DBALException, $this->databaseErrorMessage);
            case '21000':
                $this->createBadRequest($DBALException, $this->databaseRequestErrorMessage);
            case '21S01':
                $this->createBadRequest($DBALException, $this->missingPropertyErrorMessage);
            case '42S02':
                $this->createBadRequest($DBALException, $this->missingDatabaseTableMessage);
            default:
                break;
        }

        $this->createBadRequest($DBALException);
    }

    /**
     * Getter for missingDatabaseTableMessage
     */
    public function getMissingDatabaseTableMessage(): string
    {
        return $this->missingDatabaseTableMessage;
    }

    /**
     * Setter for missingDatabaseTableMessage
     */
    public function setMissingDatabaseTableMessage(
        string $missingDatabaseTableMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->missingDatabaseTableMessage = $missingDatabaseTableMessage;
        return $this;
    }

    /**
     * Getter for databaseSchemaErrorMessage
     */
    public function getDatabaseSchemaErrorMessage(): string
    {
        return $this->databaseSchemaErrorMessage;
    }

    /**
     * Setter for databaseSchemaErrorMessage
     */
    public function setDatabaseSchemaErrorMessage(
        string $databaseSchemaErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseSchemaErrorMessage = $databaseSchemaErrorMessage;
        return $this;
    }

    /**
     * Getter for querySyntaxErrorMessage
     */
    public function getQuerySyntaxErrorMessage(): string
    {
        return $this->querySyntaxErrorMessage;
    }

    /**
     * Setter for querySyntaxErrorMessage
     */
    public function setQuerySyntaxErrorMessage(
        string $querySyntaxErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->querySyntaxErrorMessage = $querySyntaxErrorMessage;
        return $this;
    }

    /**
     * Getter for entityManagementErrorMessage
     */
    public function getEntityManagementErrorMessage(): string
    {
        return $this->entityManagementErrorMessage;
    }

    /**
     * Setter for entityManagementErrorMessage
     */
    public function setEntityManagementErrorMessage(
        string $entityManagementErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->entityManagementErrorMessage = $entityManagementErrorMessage;
        return $this;
    }

    /**
     * Getter for conflictMessage
     */
    public function getConflictMessage(): string
    {
        return $this->conflictMessage;
    }

    /**
     * Setter for conflictMessage
     */
    public function setConflictMessage(string $conflictMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->conflictMessage = $conflictMessage;
        return $this;
    }

    /**
     * Getter for databaseErrorMessage
     */
    public function getDatabaseErrorMessage(): string
    {
        return $this->databaseErrorMessage;
    }

    /**
     * Setter for databaseErrorMessage
     */
    public function setDatabaseErrorMessage(string $databaseErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseErrorMessage = $databaseErrorMessage;
        return $this;
    }

    /**
     * Getter for databaseRequestErrorMessage
     */
    public function getDatabaseRequestErrorMessage(): string
    {
        return $this->databaseRequestErrorMessage;
    }

    /**
     * Setter for databaseRequestErrorMessage
     */
    public function setDatabaseRequestErrorMessage(
        string $databaseRequestErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseRequestErrorMessage = $databaseRequestErrorMessage;
        return $this;
    }

    /**
     * Getter for missingPropertyErrorMessage
     */
    public function getMissingPropertyErrorMessage(): string
    {
        return $this->missingPropertyErrorMessage;
    }

    /**
     * Setter for missingPropertyErrorMessage
     */
    public function setMissingPropertyErrorMessage(
        string $missingPropertyErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->missingPropertyErrorMessage = $missingPropertyErrorMessage;
        return $this;
    }
}
