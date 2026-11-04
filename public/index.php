<?php

declare(strict_types=1);

use Careminate\Http\Emission\NativeResponseOutput;
use Careminate\Http\Emission\ResponseEmitter;
use Careminate\Http\Kernel;
use Careminate\Http\Runtime\HttpRunner;
use Careminate\Http\ServerRequestFactory;

$kernel = require dirname(__DIR__) . '/bootstrap/http.php';

if (! $kernel instanceof Kernel) {
    throw new UnexpectedValueException(
        'Invalid HTTP bootstrap result.',
    );
}

$runner = new HttpRunner(
    new ServerRequestFactory(),
    $kernel,
    new ResponseEmitter(
        new NativeResponseOutput(),
    ),
);


/** @var array<string, mixed> $server */
$server = [];

foreach ($_SERVER as $key => $value) {
    if (! is_string($key)) {
        continue;
    }

    $server[$key] = $value;
}

try {
    $runner->runFromGlobalsSafely($server);
} finally {
    $kernel->terminate();
}

