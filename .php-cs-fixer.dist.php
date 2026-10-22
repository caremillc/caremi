<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/framework/src')
    ->in(__DIR__ . '/framework/tests')
    ->in(__DIR__ . '/bootstrap')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
    ->exclude('vendor');

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PHP8x4Migration' => true,
        'declare_strict_types' => true,
        'ordered_imports' => true,
        'no_unused_imports' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache');
