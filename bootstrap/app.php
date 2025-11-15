<?php declare(strict_types=1);

// ---------------------------------------------------------
// Define application constants
// ---------------------------------------------------------
define('CAREMI_START', microtime(true));      // Application start time
define('BASE_PATH', dirname(__DIR__));        // Project base directory
define('BOOTSTRAP_PATH', __DIR__);            // Bootstrap directory
define('PUBLIC_PATH', BASE_PATH . '/public'); // Public directory

// ---------------------------------------------------------
// Load Composer's autoloader (if available)
// ---------------------------------------------------------
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    die('Autoloader not found. Please run "composer install".');
}

// ---------------------------------------------------------
// Load performance utilities (logs execution time, etc.)
// ---------------------------------------------------------
require_once BOOTSTRAP_PATH . '/performance.php';

// ---------------------------------------------------------
// Register automatic shutdown handler for performance logging
// ---------------------------------------------------------
register_shutdown_function('logExecutionTime');