<?php

declare(strict_types=1);

use Careminate\Http\Emission\NativeResponseOutput;
use Careminate\Http\Emission\ResponseEmitter;
use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Http\Runtime\HttpRunner;
use Careminate\Http\ServerRequestFactory;
use Careminate\Session\SessionCookieConfig;
use Careminate\Session\SessionManager;

$root = dirname(__DIR__, 2);

require_once $root . '/vendor/autoload.php';

// Only accept requests originating from localhost.
$remoteAddress = $_SERVER['REMOTE_ADDR'] ?? '';

if (! in_array(
    $remoteAddress,
    ['127.0.0.1', '::1'],
    true
)) {
    http_response_code(403);
    exit;
}

$kernel = require $root . '/bootstrap/http.php';

if (! $kernel instanceof Kernel) {
    throw new RuntimeException(
        'Invalid HTTP kernel.'
    );
}

// Session diagnostics route, available only in this test server.
$kernel->router()->get(
    '/__session_probe',
    static function (Request $request): Response {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Session middleware did not start.'
            );
        }

        $visits = $_SESSION['probe_visits'] ?? 0;

        if (! is_int($visits)) {
            throw new RuntimeException(
                'Invalid diagnostic session state.'
            );
        }

        $visits++;

        $_SESSION['probe_visits'] = $visits;

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ],
            json_encode(
                ['visits' => $visits],
                JSON_THROW_ON_ERROR
            )
        );
    },
    name: 'diagnostics.session',
);

$probeSessions = new SessionManager(
    new SessionCookieConfig(
        secure: false,
        httpOnly: true,
        sameSite: 'Lax',
    ),
);

// Create a diagnostic identity.
$kernel->router()->add(
    'POST',
    '/__session_probe_login',
    static function (Request $request) use (
        $probeSessions
    ): Response {
        $probeSessions->rotateForAuthentication();

        $probeSessions->set(
            'probe_user',
            'diagnostic-user'
        );

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ],
            '{"authenticated":true}'
        );
    },
    name: 'diagnostics.session.login',
);

// Check whether the diagnostic identity exists.
$kernel->router()->get(
    '/__session_probe_identity',
    static function (Request $request) use (
        $probeSessions
    ): Response {
        $authenticated = $probeSessions->get(
            'probe_user'
        ) === 'diagnostic-user';

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ],
            json_encode(
                ['authenticated' => $authenticated],
                JSON_THROW_ON_ERROR
            )
        );
    },
    name: 'diagnostics.session.identity',
);

// Log out using the actual SessionManager.
$kernel->router()->add(
    'POST',
    '/__session_probe_logout',
    static function (Request $request) use (
        $probeSessions
    ): Response {
        $probeSessions->logout();

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ],
            '{"logged_out":true}'
        );
    },
    name: 'diagnostics.session.logout',
);

$kernel->router()->get(
    '/__session_probe_failure',
    static function (Request $request): Response {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'Session was not started before handler execution.'
            );
        }

        $_SESSION['probe_before_failure'] = 'persisted';

        throw new RuntimeException(
            'Intentional session diagnostic failure.'
        );
    },
    name: 'diagnostics.session.failure',
);

$kernel->router()->get(
    '/__session_probe_recovery',
    static function (Request $request): Response {
        $recovered = (
            $_SESSION['probe_before_failure'] ?? null
        ) === 'persisted';

        return new Response(
            200,
            [
                'Content-Type' => 'application/json',
                'Cache-Control' => 'no-store',
            ],
            json_encode(
                ['recovered' => $recovered],
                JSON_THROW_ON_ERROR
            )
        );
    },
    name: 'diagnostics.session.recovery',
);


$runner = new HttpRunner(
    new ServerRequestFactory(),
    $kernel,
    new ResponseEmitter(
        new NativeResponseOutput()
    )
);

/** @var array<string, mixed> $server */
$server = [];

foreach ($_SERVER as $key => $value) {
    if (is_string($key)) {
        $server[$key] = $value;
    }
}

try {
    $runner->runFromGlobalsSafely($server);
} finally {
    $kernel->terminate();
}
