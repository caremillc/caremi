<?php

declare(strict_types=1);

use Careminate\Config\Configuration;
use Careminate\Foundation\Application;
use Careminate\Http\Kernel;
use Careminate\Http\Middleware\CsrfMiddleware;
use Careminate\Http\Middleware\SessionMiddleware;
use Careminate\Http\Request;
use Careminate\Http\Routing\Router;
use Careminate\Security\CsrfTokenManager;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;

// Use the same route registry as the application CLI.
$router = require __DIR__ . '/routes.php';

if (! $router instanceof Router) {
    throw new UnexpectedValueException(
        'Invalid route bootstrap result.'
    );
}

// Construct the application without booting it.
// The HTTP kernel is responsible for booting.
$application = require __DIR__ . '/application.php';

if (! $application instanceof Application) {
    throw new UnexpectedValueException(
        'Invalid application bootstrap result.'
    );
}

// Load validated application configuration.
$configuration = $application->container()->get(
    Configuration::class
);

if (! $configuration instanceof Configuration) {
    throw new LogicException(
        'Application configuration unavailable.'
    );
}

$environment = $configuration->string(
    'app.environment'
);

// Local and testing environments may use HTTP.
// Staging and production require secure session cookies.
$secureCookies = ! in_array(
    $environment,
    ['local', 'testing'],
    true
);

// Construct the HTTP kernel.
$kernel = new Kernel(
    $application,
    $router
);

// Register session middleware before application boot.
$sessionManager = new SessionManager(
    new SessionCookieConfig(
        secure: $secureCookies,
        httpOnly: true,
        sameSite: 'Lax'
    )
);

$kernel->middleware(
    new SessionMiddleware($sessionManager)
);

$csrfTokens = new CsrfTokenManager(
    $sessionManager
);

$kernel->middleware(
    new CsrfMiddleware(
        $csrfTokens,
        isExempt: static function (Request $request): bool {
            return $request->method === 'POST'
                && $request->path() === '/api/echo';
        },
    )
);

return $kernel;
