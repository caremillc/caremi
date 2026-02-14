<?php declare(strict_types=1); // public/index.php

// bootstrapping
require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/performance.php';


// request received
// request received
$request = \Careminate\Http\Requests\Request::createFromGlobals();

// perform some logic

// send response (string of content)
$kernel = new \Careminate\Http\Kernel();

$response = $kernel->handle($request);

$response->send();

// dd($response);