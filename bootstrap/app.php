<?php

declare(strict_types=1);

use Careminate\Foundation\Application;

// Preserve environmentOverrides, externalEnvironment and
// environmentFile passed by existing bootstrap callers.
$application = require __DIR__ . '/application.php';

if (! $application instanceof Application) {
    throw new UnexpectedValueException(
        'Invalid application bootstrap result.'
    );
}

$application->boot();

return $application;
