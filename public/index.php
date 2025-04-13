<?php declare(strict_types=1);
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);
define('ROOT_DIR', __DIR__ . '/../public');
define('PUBLIC_PATH', __DIR__);


require_once BASE_PATH . '/bootstrap/app.php';

 // Display the current timezone and time for debugging purposes
 var_dump(date_default_timezone_get(), date('h:i:s'));

try {
    // Bootstrap the app
$app = (new Careminate\Application())->start();

} catch (\Throwable $e) {
    http_response_code(500);
    echo "Application error: " . $e->getMessage();
} 

// Display timezone and time again after application startup
var_dump(date_default_timezone_get(), date('h:i:s'));