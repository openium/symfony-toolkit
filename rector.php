<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/Command',
            __DIR__ . '/Controller',
            __DIR__ . '/DependencyInjection',
            __DIR__ . '/Entity',
            __DIR__ . '/EventListener',
            __DIR__ . '/Exception',
            __DIR__ . '/Service',
            __DIR__ . '/Tests',
            __DIR__ . '/Utils',
        ]
    );
    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    // define sets of rules
    $rectorConfig->sets(
        [
            LevelSetList::UP_TO_PHP_80,
            SetList::DEAD_CODE,
            SetList::CODE_QUALITY
        ]
    );
};
