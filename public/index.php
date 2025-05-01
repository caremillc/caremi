<?php declare(strict_types=1);

require_once __DIR__ . '/../bootstrap/app.php';

$request = \Careminate\Http\Requests\Request::createFromGlobals();
$response = $app->start($request);
$app->terminate($request, $response);