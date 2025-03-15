<?php declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

try {
    $httpKernel = new Careminate\Http\HttpKernel();
    $httpKernel->start();
} catch (\Throwable $e) {
    http_response_code(500);
    echo "Application error: " . $e->getMessage();
}