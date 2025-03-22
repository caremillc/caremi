<?php declare(strict_types=1); // public/index.php

use Careminate\Http\Requests\Request;


define('CAREMI_START', microtime(true));

// Include Composer autoload to load dependencies
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Capture the current timestamp to calculate the processing time
$requestStartTime = microtime(true);

// request received
$request = Request::createFromGlobals();

dd($request);

// Calculate the total processing time
$requestEndTime = microtime(true);
$executionTime = $requestEndTime - $requestStartTime;

// Log the execution time (this could be saved to a file or monitored)
echo "Request processed in " . number_format($executionTime, 4) . " seconds.\n";

// Send a response
echo 'Hello World from index';