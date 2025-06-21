<?php declare(strict_types=1);

use Careminate\Http\Responses\Response;

define('BASE_PATH', dirname(__DIR__));

// Autoload classes
require_once BASE_PATH . '/vendor/autoload.php';

// Load .env environment variables
Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

// Example usage
$response = new Response('Hello World! from index');
$response->send();
