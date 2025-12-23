<?php declare(strict_types=1);

// ---------------------------------------------------------
// Define application constants
// ---------------------------------------------------------
define('CAREMI_START', microtime(true));      // Application start time
define('BASE_PATH', dirname(__DIR__));        // Project base directory
define('BOOTSTRAP_PATH', __DIR__);            // Bootstrap directory
define('CONFIG_PATH', BASE_PATH . '/config'); // Config directory
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
// Simple .env loader
// ---------------------------------------------------------
//$envFile = __DIR__ . '/../.env';

$envFile = base_path('.env');

if (!file_exists($envFile)) {
    $exampleFile = base_path('.env.example');

    if (file_exists($exampleFile)) {
        // Read content from .env.example
        $exampleContent = file_get_contents($exampleFile);

        if ($exampleContent === false) {
            die("<p style='color:red;'>Error: Failed to read .env.example file.</p>");
        }

        // Write content into .env
        $result = file_put_contents($envFile, $exampleContent);

        if ($result === false) {
            die("<p style='color:red;'>Error: Failed to create .env file from .env.example. Check folder permissions.</p>");
        }

        echo "<p style='color:orange;'>Warning: .env not found. A new one has been created from .env.example.</p>";
    } else {
        die("<p style='color:red;'>Error: No .env or .env.example file found. Please create one in the project root.</p>");
    }
}

// Continue with loader
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value, " \t\n\r\0\x0B\"'");
    putenv("{$key}={$value}");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}



// ---------------------------------------------------------
// Set error handling
// ---------------------------------------------------------
if (!function_exists('app_debug_mode')) {
    function app_debug_mode(): bool
    {
        return filter_var(env('APP_DEBUG') ?? false, FILTER_VALIDATE_BOOL);
    }
}

if (app_debug_mode()) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// ---------------------------------------------------------
// Register global shutdown handler
// ---------------------------------------------------------
// register_shutdown_function('logExecutionTime');



