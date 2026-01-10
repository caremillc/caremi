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

// ===========================================
// MANUAL AUTOLOAD REGISTRATION (Temporary)
// ===========================================
spl_autoload_register(function ($class) use ($basePath) {
    // Careminate namespace
    if (str_starts_with($class, 'Careminate\\')) {
        $file = $basePath . '/framework/src/' . str_replace('\\', '/', substr($class, 11)) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    
    // App namespace
    if (str_starts_with($class, 'App\\')) {
        $file = $basePath . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
        if (file_exists($file)) {
            require $file;
            return true;
        }
    }
    
    return false;
});

// Create the application instance
$app = new \Careminate\Foundation\Application($basePath);

// dd($app);
// Return the application instance
return $app;
