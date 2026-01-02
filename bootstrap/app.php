<?php declare(strict_types=1);
// ---------------------------------------------------------
// Define application constants
// ---------------------------------------------------------
define('CAREMI_START', microtime(true));      // Application start time
define('BASE_PATH', dirname(__DIR__));        // Project base directory
define('BOOTSTRAP_PATH', __DIR__);            // Bootstrap directory
define('CONFIG_PATH', BASE_PATH . '/config'); // Config directory
define('PUBLIC_PATH', BASE_PATH . '/public'); // Public directory

/**
 * Bootstrap the application
 */

// Define base path
$basePath = dirname(__DIR__);

// ---------------------------------------------------------
// Load Composer's autoloader (if available)
// ---------------------------------------------------------
$autoloadPath = $basePath . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require $autoloadPath;
} else {
    die('Autoloader not found. Please run "composer install".');
}


// Create the application instance
$app = new \Careminate\Foundation\Application($basePath);

// dd($app);
// Return the application instance
return $app;