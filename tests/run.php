<?php

declare(strict_types=1);

use PHPUnit\TextUI\Application;

$rootPath = dirname(__DIR__);

require $rootPath . '/vendor/autoload.php';

$arguments = [
    $_SERVER['argv'][0] ?? 'tests/run.php',
    '--configuration',
    $rootPath . '/phpunit.xml',
    ...array_slice($_SERVER['argv'] ?? [], 1),
];

exit((new Application())->run($arguments));