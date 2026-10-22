<?php

declare(strict_types=1);

use Careminate\Config\Configuration;
use Careminate\Foundation\Application;
use Careminate\Foundation\Framework;

$environmentOverrides = [
    'APP_NAME' => 'Careminate',
    'APP_ENV' => 'testing',
    'APP_DEBUG' => 'false',
    'APP_PORT' => '8080',
];

$environmentFile = dirname(__DIR__) . '/.env.smoke-nonexistent';

$application = require dirname(__DIR__) . '/bootstrap/app.php';

if (!$application instanceof Application) {
    throw new RuntimeException('Bootstrap did not return an application.');
}

if (!$application->isBooted()) {
    throw new RuntimeException('Application was not booted.');
}

$config = $application->container()->get(Configuration::class);

if (!$config instanceof Configuration) {
    throw new RuntimeException('Configuration was not registered.');
}

if ($config->string('app.environment') !== 'testing') {
    throw new RuntimeException('Unexpected application environment.');
}

echo sprintf(
    "Careminate skeleton bootstrapped successfully.\nFramework: %s\nPHP: %s\n",
    Framework::version(),
    PHP_VERSION
);

$application->terminate();
