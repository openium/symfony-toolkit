<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Command',
        __DIR__ . '/Controller',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Entity',
        __DIR__ . '/EventListener',
        __DIR__ . '/Exception',
        __DIR__ . '/Service',
        __DIR__ . '/Tests',
        __DIR__ . '/Utils',
    ])
    ->withRules([
        InlineConstructorDefaultToPropertyRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
    ])
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        naming: true,
        symfonyCodeQuality: true,
    )
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withSets([
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        PHPUnitSetList::PHPUNIT_110,
    ])
    ->withTypeCoverageLevel(0);
