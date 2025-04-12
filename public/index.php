<?php declare(strict_types=1);
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);
define('ROOT_DIR', __DIR__ . '/../public');

require_once BASE_PATH . '/bootstrap/app.php';

try {
    (new \Careminate\Application)->start();
    
} catch (\Throwable $e) {
    http_response_code(500);
    echo "Application error: " . $e->getMessage();
} 