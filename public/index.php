<?php declare(strict_types=1);

// bootstrapping
require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/performance.php';

// request received
$request = \Careminate\Http\Requests\Request::createFromGlobals();

// instantiate router
$router = new \Careminate\Routing\Router();

// instantiate exception handler
$handler = new \Careminate\Exceptions\Handler();

// instantiate kernel (router + handler)
$kernel = new \Careminate\Http\Kernel($router, $handler);

// handle request
$response = $kernel->handle($request);

// send response
$response->send();
