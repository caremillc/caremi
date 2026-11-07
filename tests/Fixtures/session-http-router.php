<?php

declare(strict_types=1);

use Careminate\Http\Emission\NativeResponseOutput;
use Careminate\Http\Emission\ResponseEmitter;
use Careminate\Http\Kernel;
use Careminate\Http\Request;
use Careminate\Http\Response;
use Careminate\Http\Runtime\HttpRunner;
use Careminate\Http\ServerRequestFactory;

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
