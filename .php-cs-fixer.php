<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/framework/src',
        __DIR__ . '/framework/tests',
        __DIR__ . '/tests',
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'native_function_invocation' => [
            'include' => ['@compiler_optimized'],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'no_superfluous_phpdoc_tags' => true,
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'php_unit_attributes' => true,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_order' => true,
        'single_quote' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);

