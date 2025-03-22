<?php declare(strict_types=1); // public/index.php

use Careminate\Http\Kernel;
use Careminate\Http\Requests\Request;

define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', dirname(__FILE__));

// Capture the current timestamp to calculate the processing time
$requestStartTime = microtime(true);

// Include Composer autoload to load dependencies
require_once dirname(__DIR__) . '/vendor/autoload.php';

// passing the kernel to the Container
$container = require BASE_PATH . '/config/container.php';

// request received
$request = Request::createFromGlobals();

// Initializes the application's kernel 
$kernel =  $container->get(Kernel::class); 

// send response (string of content)
$response = $kernel->handle($request);

$response->send();

// Calculate the total processing time
$requestEndTime = microtime(true);
$executionTime = $requestEndTime - $requestStartTime;
// Log the execution time (this could be saved to a file or monitored)
echo "Request processed in " . number_format($executionTime, 4) . " seconds.\n";

dd($response);