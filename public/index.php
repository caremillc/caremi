<?php

declare(strict_types=1);

use App\Services\FrameworkStatus;
use Careminate\Contracts\Application\ApplicationInterface;
use Careminate\Foundation\ApplicationState;

/** @var ApplicationInterface $app */
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->beginScope('http-' . bin2hex(random_bytes(8)));

try {
    $app->boot();
    $payload = $app->make(FrameworkStatus::class)->report();
    $statusCode = 200;
} catch (Throwable $exception) {
    error_log(sprintf(
        '[Careminate] %s: %s in %s:%d',
        $exception::class,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
    ));

    $payload = [
        'name' => 'Careminate',
        'status' => 'error',
        'message' => 'The application could not process the request.',
    ];
    $statusCode = 500;
} finally {
    if (in_array($app->state(), [ApplicationState::Booted, ApplicationState::Failed], true)) {
        try {
            $app->terminate();
        } catch (Throwable $terminationFailure) {
            error_log(sprintf(
                '[Careminate termination] %s: %s',
                $terminationFailure::class,
                $terminationFailure->getMessage(),
            ));
        }
    } elseif ($app->scopeActive()) {
        $app->endScope();
    }
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
http_response_code($statusCode);

echo json_encode(
    $payload,
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT,
);

