<?php declare(strict_types=1); // public/index.php

// bootstrapping
require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/performance.php';


// request received
// request received
$request = \Careminate\Http\Requests\Request::createFromGlobals();

// Then use it anywhere
$user = ['name' => 'John', 'age' => 30];
$data = new stdClass();
$data->items = [1, 2, 3];

dd($user, $data);

dd($request);

// perform some logic

// send response (string of content)
echo 'Hello World';