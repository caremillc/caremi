<?php declare(strict_types=1);

$app = require_once __DIR__ . '/../bootstrap/app.php';

$request = request();

$response = json([
    'app' => config('app.name'),
    'method' => $request?->method(),
    'path' => $request?->path(),
    'url' => $request?->fullUrl(),
    'query' => $request?->query(),
    'input' => $request?->all(),
    'ip' => $request?->ip(),
]);

$response->send();