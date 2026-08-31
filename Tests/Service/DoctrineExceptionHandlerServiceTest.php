<?php

namespace Openium\SymfonyToolKitBundle\Tests\Service;

use Doctrine\DBAL\Driver\Exception as DriverExceptionInterface;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Openium\SymfonyToolKitBundle\Service\DoctrineExceptionHandlerService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DoctrineExceptionHandlerServiceTest
 *
 * @package Openium\SymfonyToolKitBundle\Test\Service
 */
#[CoversNothing]
class DoctrineExceptionHandlerServiceTest extends TestCase
{
    private function createIdentityTranslatorStub(): TranslatorInterface
    {
        $translator = $this->createStub(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);
        return $translator;
    }

    private function createRealTranslator(string $locale = 'en'): TranslatorInterface
    {
        $translator = new Translator($locale);
        $translator->addLoader('yaml', new YamlFileLoader());
        foreach (['en', 'fr'] as $availableLocale) {
            $translator->addResource(
                'yaml',
                __DIR__ . '/../../Resources/translations/openium_symfony_toolkit.' . $availableLocale . '.yaml',
                $availableLocale,
                'openium_symfony_toolkit'
            );
        }
        return $translator;
    }

    private function createDriverExceptionStub(): DriverExceptionInterface
    {
        return new class extends \Exception implements DriverExceptionInterface {
            #[\Override]
            public function getSQLState(): ?string
            {
                return null;
            }
        };
    }

    public function testLog(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $throwable = $this->createStub(\Exception::class);
        $logger->expects($this->exactly(5))->method('error');
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        self::assertTrue($doctrineExceptionHandlerService instanceof DoctrineExceptionHandlerService);
        $doctrineExceptionHandlerService->toHttpException($exception);
    }

    public function testToHttpExceptionHandlesSubclassOfKnownException(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $driverStub = $this->createDriverExceptionStub();
        // a subclass of TableNotFoundException that switch($throwable::class) could never match
        $throwable = new class ($driverStub, null) extends TableNotFoundException {
        };
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createRealTranslator()
        );
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
        $driverStub = $this->createDriverExceptionStub();
        // a plain DriverException, matching none of the more specific subclasses
        $throwable = new DriverException($driverStub, null);
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createRealTranslator()
        );
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createRealTranslator()
        );
        // then
        self::expectException(ConflictHttpException::class);
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
    }

    public function testToHttpExceptionUsesFrenchTranslationWhenLocaleIsFr(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $driverStub = $this->createDriverExceptionStub();
        $throwable = new TableNotFoundException($driverStub, null);
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createRealTranslator('fr')
        );
        // then
        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Table de base de données manquante');
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
    }

    public function testCustomMessageOverrideBypassesTranslation(): void
    {
        // given: a real translator, whose catalog has no entry for this literal message,
        // must fall back to returning it unchanged - proving setters still take literal
        // messages, not just translation ids.
        $logger = $this->createStub(LoggerInterface::class);
        $driverStub = $this->createDriverExceptionStub();
        $throwable = new TableNotFoundException($driverStub, null);
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createRealTranslator('fr')
        );
        $doctrineExceptionHandlerService->setMissingDatabaseTableMessage('Message personnalisé');
        // then
        self::expectException(BadRequestHttpException::class);
        self::expectExceptionMessage('Message personnalisé');
        // when
        $doctrineExceptionHandlerService->toHttpException($throwable);
    }

    public function testMissingDatabaseTableMessage(): void
    {
        // given
        $logger = $this->createStub(LoggerInterface::class);
        $message = "new error message";
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.missing_database_table",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.database_schema_error",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.query_syntax_error",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.entity_management_error",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.conflict",
            $doctrineExceptionHandlerService->getConflictMessage()
        );
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.database_error",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.database_request_error",
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
        $doctrineExceptionHandlerService = new DoctrineExceptionHandlerService(
            $logger,
            $this->createIdentityTranslatorStub()
        );
        // then
        static::assertEquals(
            "doctrine_exception.missing_property_error",
            $doctrineExceptionHandlerService->getMissingPropertyErrorMessage()
        );
        // when
        $doctrineExceptionHandlerService->setMissingPropertyErrorMessage($message);
        // then
        static::assertEquals($message, $doctrineExceptionHandlerService->getMissingPropertyErrorMessage());
    }
}
