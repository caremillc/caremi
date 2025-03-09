<?php declare(strict_types=1);

use Dotenv\Dotenv;

define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', dirname(__FILE__));
define('ROOT_DIR', __DIR__);

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(base_path());
$dotenv->load();

 // Display the current timezone and time for debugging purposes
 var_dump(date_default_timezone_get(), date('h:i:s'));

try {
    // Initialize the HttpKernel
    $kernel = new App\Http\Kernel();

    (new \Careminate\Application)->start();
    // Proceed with request handling (e.g., routing, controllers, etc.)

} catch (RuntimeException $e) {
    // Handle the exceptions thrown by Kernel
    http_response_code(500); // Internal Server Error
    echo $e->getMessage();
}

// Display timezone and time again after application startup
var_dump(date_default_timezone_get(), date('h:i:s'));
