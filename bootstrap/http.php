<?php

declare(strict_types=1);

use Careminate\Foundation\Application;
use Careminate\Http\Kernel;
use Careminate\Http\Routing\Router;

// Use the same route registry as the application CLI.
$router = require __DIR__ . '/routes.php';

if (! $router instanceof Router) {
    throw new UnexpectedValueException(
        'Invalid route bootstrap result.',
    );
}

// Existing skeleton bootstrap loads configuration, providers,
// and returns a booted Application.
$application = require __DIR__ . '/app.php';

if (! $application instanceof Application) {
    throw new UnexpectedValueException(
        'Invalid application bootstrap result.',
    );
}

return new Kernel(
    $application,
    $router,
);
