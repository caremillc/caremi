<?php

declare(strict_types=1);

use Careminate\Foundation\Framework;

$bootstrap = dirname(__DIR__) . '/bootstrap/app.php';

try {
    $result = require $bootstrap;

    if (!is_array($result)) {
        throw new RuntimeException(
            'Application bootstrap must return an array.'
        );
    }

    if (!class_exists(Framework::class)) {
        throw new RuntimeException(
            'Careminate framework could not be autoloaded.'
        );
    }

    if (($result['framework'] ?? null) !== Framework::version()) {
        throw new RuntimeException(
            'Framework version mismatch.'
        );
    }

    if (($result['php'] ?? null) !== PHP_VERSION) {
        throw new RuntimeException(
            'PHP runtime version mismatch.'
        );
    }

    if (!Framework::supportsRuntime()) {
        throw new RuntimeException(
            'Unsupported PHP runtime.'
        );
    }

    echo sprintf(
        "Careminate skeleton bootstrapped successfully.\n"
        . "Framework: %s\n"
        . "PHP: %s\n",
        Framework::version(),
        PHP_VERSION
    );

    exit(0);
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        "Bootstrap test failed: "
        . $exception->getMessage()
        . PHP_EOL
    );

    exit(1);
}
