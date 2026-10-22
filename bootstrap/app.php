<?php

declare(strict_types=1);

use Careminate\Config\Configuration;
use Careminate\Config\ConfigurationLoader;
use Careminate\Config\ConfigurationValidator;
use Careminate\Config\Environment;
use Careminate\Foundation\Application;
use Careminate\Foundation\ConfiguredProviderLoader;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);

$bootstrapVariables = get_defined_vars();

$environmentOverrides = $bootstrapVariables['environmentOverrides'] ?? [];
$externalEnvironment = $bootstrapVariables['externalEnvironment'] ?? [];
$environmentFile = $bootstrapVariables['environmentFile']
    ?? $root . '/.env';

if (!is_array($environmentOverrides)) {
    throw new InvalidArgumentException(
        'Environment overrides must be an array.'
    );
}

if (!is_array($externalEnvironment)) {
    throw new InvalidArgumentException(
        'External environment must be an array.'
    );
}

if (!is_string($environmentFile) || $environmentFile === '') {
    throw new InvalidArgumentException(
        'Environment file path must be a non-empty string.'
    );
}

foreach ($environmentOverrides as $key => $value) {
    if (!is_string($key) || !is_string($value)) {
        throw new InvalidArgumentException(
            'Environment overrides must contain string keys and values.'
        );
    }
}

foreach ($externalEnvironment as $key => $value) {
    if (!is_string($key) || !is_string($value)) {
        throw new InvalidArgumentException(
            'External environment must contain string keys and values.'
        );
    }
}

/** @var array<string, string> $environmentOverrides */
/** @var array<string, string> $externalEnvironment */

$environment = Environment::fromFile(
    $environmentFile,
    $externalEnvironment,
    [
        'APP_NAME' => 'Careminate',
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'APP_PORT' => '8080',
    ],
    $environmentOverrides
);

$configuration = new ConfigurationLoader()->load(
    $root . '/config',
    $environment
);

new ConfigurationValidator()->validate(
    $configuration,
    [
        'app.name' => 'non-empty-string',
        'app.environment' => 'non-empty-string',
        'app.debug' => 'boolean',
        'app.port' => 'integer',
    ]
);

$applicationEnvironment = $configuration->string('app.environment');

if (!in_array(
    $applicationEnvironment,
    ['local', 'testing', 'staging', 'production'],
    true
)) {
    throw new InvalidArgumentException(
        'Configuration key "app.environment" contains an unsupported environment.'
    );
}

$port = $configuration->integer('app.port');

if ($port < 1 || $port > 65535) {
    throw new InvalidArgumentException(
        'Configuration key "app.port" must be between 1 and 65535.'
    );
}

if (
    $applicationEnvironment === 'production'
    && $configuration->boolean('app.debug')
) {
    throw new InvalidArgumentException(
        'Debug mode must be disabled in production.'
    );
}

$application = new Application();

$application->container()->instance(
    Configuration::class,
    $configuration
);

new ConfiguredProviderLoader()->register(
    $application,
    $configuration
);

$application->boot();

return $application;
