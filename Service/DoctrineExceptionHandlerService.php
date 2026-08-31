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
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;
use UnexpectedValueException;

/**
 * Class DoctrineExceptionHandlerService
 *
 * @package Openium\SymfonyToolKitBundle\Service
 */
class DoctrineExceptionHandlerService implements DoctrineExceptionHandlerServiceInterface
{
    private const TRANSLATION_DOMAIN = 'openium_symfony_toolkit';

    private string $missingDatabaseTableMessage = 'doctrine_exception.missing_database_table';

    private string $databaseSchemaErrorMessage = 'doctrine_exception.database_schema_error';

    private string $querySyntaxErrorMessage = 'doctrine_exception.query_syntax_error';

    private string $entityManagementErrorMessage = 'doctrine_exception.entity_management_error';

    private string $conflictMessage = 'doctrine_exception.conflict';

    private string $databaseErrorMessage = 'doctrine_exception.database_error';

    private string $databaseRequestErrorMessage = 'doctrine_exception.database_request_error';

    private string $missingPropertyErrorMessage = 'doctrine_exception.missing_property_error';

    /**
     * ExceptionHandlerService constructor.
     */
    public function __construct(
        protected LoggerInterface $logger,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * Log an exception information for debug
     */
    #[\Override]
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
    #[\Override]
    public function toHttpException(Throwable $throwable): never
    {
        // Call the logger
        $this->log($throwable);
        // Select the process. Ordered from the most specific type to the most generic one:
        // instanceof matches subclasses too, unlike the previous switch($throwable::class),
        // which silently missed any subclass not explicitly listed (e.g. it never matched
        // Exception::class, an interface no concrete throwable's ::class can ever equal).
        match (true) {
            $throwable instanceof TableNotFoundException
                => $this->createBadRequest($throwable, $this->missingDatabaseTableMessage),
            $throwable instanceof TableExistsException,
            $throwable instanceof NonUniqueFieldNameException
                => $this->createBadRequest($throwable, $this->databaseSchemaErrorMessage),
            $throwable instanceof SyntaxErrorException
                => $this->createBadRequest($throwable, $this->querySyntaxErrorMessage),
            $throwable instanceof UniqueConstraintViolationException,
            $throwable instanceof ForeignKeyConstraintViolationException
                => $this->createConflict($throwable, $this->conflictMessage),
            $throwable instanceof NotNullConstraintViolationException,
            $throwable instanceof ORMInvalidArgumentException,
            $throwable instanceof UnexpectedValueException
                => $this->createBadRequest($throwable),
            // Generic driver-level exception not matched by a more specific case above.
            $throwable instanceof DriverException
                => $this->createBadRequest($throwable, $this->databaseSchemaErrorMessage),
            $throwable instanceof Exception
                => $this->dbalExceptionManagement($throwable),
            default => throw $throwable,
        };
    }

    /**
     * createBadRequest
     *
     * @throws BadRequestHttpException
     */
    protected function createBadRequest(Throwable $throwable, ?string $message = null): never
    {
        throw new BadRequestHttpException(
            $this->translate($message ?? $this->entityManagementErrorMessage),
            $throwable
        );
    }

    /**
     * createConflict
     *
     * @throws ConflictHttpException
     */
    protected function createConflict(Throwable $throwable, ?string $message = null): never
    {
        if ($throwable->getPrevious() instanceof \Throwable) {
            $this->logger->error($throwable->getPrevious()->getCode());
        }

        throw new ConflictHttpException($this->translate($message ?? $this->conflictMessage), $throwable);
    }

    /**
     * Translates a message key through the "openium_symfony_toolkit" domain.
     *
     * Falls back to returning the input unchanged when it is not a known translation id
     * (e.g. a literal message set via one of the setters below), matching Translator's own
     * fallback behavior.
     */
    private function translate(string $message): string
    {
        return $this->translator->trans($message, [], self::TRANSLATION_DOMAIN);
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
    #[\Override]
    public function getMissingDatabaseTableMessage(): string
    {
        return $this->missingDatabaseTableMessage;
    }

    /**
     * Setter for missingDatabaseTableMessage
     */
    #[\Override]
    public function setMissingDatabaseTableMessage(
        string $missingDatabaseTableMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->missingDatabaseTableMessage = $missingDatabaseTableMessage;
        return $this;
    }

    /**
     * Getter for databaseSchemaErrorMessage
     */
    #[\Override]
    public function getDatabaseSchemaErrorMessage(): string
    {
        return $this->databaseSchemaErrorMessage;
    }

    /**
     * Setter for databaseSchemaErrorMessage
     */
    #[\Override]
    public function setDatabaseSchemaErrorMessage(
        string $databaseSchemaErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseSchemaErrorMessage = $databaseSchemaErrorMessage;
        return $this;
    }

    /**
     * Getter for querySyntaxErrorMessage
     */
    #[\Override]
    public function getQuerySyntaxErrorMessage(): string
    {
        return $this->querySyntaxErrorMessage;
    }

    /**
     * Setter for querySyntaxErrorMessage
     */
    #[\Override]
    public function setQuerySyntaxErrorMessage(
        string $querySyntaxErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->querySyntaxErrorMessage = $querySyntaxErrorMessage;
        return $this;
    }

    /**
     * Getter for entityManagementErrorMessage
     */
    #[\Override]
    public function getEntityManagementErrorMessage(): string
    {
        return $this->entityManagementErrorMessage;
    }

    /**
     * Setter for entityManagementErrorMessage
     */
    #[\Override]
    public function setEntityManagementErrorMessage(
        string $entityManagementErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->entityManagementErrorMessage = $entityManagementErrorMessage;
        return $this;
    }

    /**
     * Getter for conflictMessage
     */
    #[\Override]
    public function getConflictMessage(): string
    {
        return $this->conflictMessage;
    }

    /**
     * Setter for conflictMessage
     */
    #[\Override]
    public function setConflictMessage(string $conflictMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->conflictMessage = $conflictMessage;
        return $this;
    }

    /**
     * Getter for databaseErrorMessage
     */
    #[\Override]
    public function getDatabaseErrorMessage(): string
    {
        return $this->databaseErrorMessage;
    }

    /**
     * Setter for databaseErrorMessage
     */
    #[\Override]
    public function setDatabaseErrorMessage(string $databaseErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseErrorMessage = $databaseErrorMessage;
        return $this;
    }

    /**
     * Getter for databaseRequestErrorMessage
     */
    #[\Override]
    public function getDatabaseRequestErrorMessage(): string
    {
        return $this->databaseRequestErrorMessage;
    }

    /**
     * Setter for databaseRequestErrorMessage
     */
    #[\Override]
    public function setDatabaseRequestErrorMessage(
        string $databaseRequestErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->databaseRequestErrorMessage = $databaseRequestErrorMessage;
        return $this;
    }

    /**
     * Getter for missingPropertyErrorMessage
     */
    #[\Override]
    public function getMissingPropertyErrorMessage(): string
    {
        return $this->missingPropertyErrorMessage;
    }

    /**
     * Setter for missingPropertyErrorMessage
     */
    #[\Override]
    public function setMissingPropertyErrorMessage(
        string $missingPropertyErrorMessage
    ): DoctrineExceptionHandlerServiceInterface {
        $this->missingPropertyErrorMessage = $missingPropertyErrorMessage;
        return $this;
    }
}
