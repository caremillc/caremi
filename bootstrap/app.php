<?php declare(strict_types=1);

// Define application constants
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);

// Include Composer autoload
require_once BASE_PATH . '/vendor/autoload.php';

use Careminate\Foundation\Application;

// Create the application instance
$app = new Application(BASE_PATH);

// load .env

// initialize container

// register service providers

// load config

// register routes

// boot services

return $app;