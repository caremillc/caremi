<?php declare(strict_types=1);

use Careminate\Http\Responses\Response;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

// Default usage (with buffering)
$response = new Response('Hello World');
$response->send();

// dd($response);

$response->send();
