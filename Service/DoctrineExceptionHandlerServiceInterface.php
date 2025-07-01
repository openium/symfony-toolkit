<?php

namespace Openium\SymfonyToolKitBundle\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Throwable;

/**
 * Interface DoctrineExceptionHandlerServiceInterface
 *
 * @package  Openium\SymfonyToolKitBundle\Service
 */
interface DoctrineExceptionHandlerServiceInterface
{
    /**
     * Log an exception information for debug
     */
    public function log(Throwable $throwable): void;

    /**
     * toHttpException
     * Catch & Process the throwable
     *
     * @throws BadRequestHttpException
     * @throws ConflictHttpException
     */
    public function toHttpException(Throwable $throwable): never;

    public function getMissingDatabaseTableMessage(): string;

    public function setMissingDatabaseTableMessage(
        string $missingDatabaseTableMessage
    ): DoctrineExceptionHandlerServiceInterface;

    public function getDatabaseSchemaErrorMessage(): string;

    public function setDatabaseSchemaErrorMessage(
        string $databaseSchemaErrorMessage
    ): DoctrineExceptionHandlerServiceInterface;

    public function getQuerySyntaxErrorMessage(): string;

    public function setQuerySyntaxErrorMessage(
        string $querySyntaxErrorMessage
    ): DoctrineExceptionHandlerServiceInterface;

    public function getEntityManagementErrorMessage(): string;

    public function setEntityManagementErrorMessage(
        string $entityManagementErrorMessage
    ): DoctrineExceptionHandlerServiceInterface;

    public function getConflictMessage(): string;

    public function setConflictMessage(string $conflictMessage): DoctrineExceptionHandlerServiceInterface;

    public function getDatabaseErrorMessage(): string;

    public function setDatabaseErrorMessage(string $databaseErrorMessage): DoctrineExceptionHandlerServiceInterface;

    public function getDatabaseRequestErrorMessage(): string;

    public function setDatabaseRequestErrorMessage(
        string $databaseRequestErrorMessage
    ): DoctrineExceptionHandlerServiceInterface;

    public function getMissingPropertyErrorMessage(): string;

    public function setMissingPropertyErrorMessage(
        string $missingPropertyErrorMessage
    ): DoctrineExceptionHandlerServiceInterface;
}
