<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/modules',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
        __DIR__ . '/frameworkv1/src',
        __DIR__ . '/frameworkv1/tests',
    ])
    ->withSkip([
        __DIR__ . '/vendor',
        __DIR__ . '/storage',
    ])
    ->withPhpSets(php84: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    );
