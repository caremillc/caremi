<?php

declare(strict_types=1);

use Careminate\Foundation\Framework;

$autoload = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_file($autoload)) {
    throw new RuntimeException(
        'Composer dependencies are missing. Run composer install.'
    );
}

require_once $autoload;

if (!Framework::supportsRuntime()) {
    throw new RuntimeException(
        'Careminate requires PHP 8.4 or newer.'
    );
}

return [
    'framework' => Framework::version(),
    'php' => PHP_VERSION,
];
