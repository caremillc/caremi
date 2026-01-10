<?php declare(strict_types=1);

use Careminate\Http\Requests\Request;
use Careminate\Exceptions\ExceptionHandler;

/**
 * --------------------------------------------------------------
 * Define the path to Composer's autoloader
 * --------------------------------------------------------------
 */
$autoload = dirname(__DIR__) . '/vendor/autoload.php';

/**
 * --------------------------------------------------------------
 * Ensure the vendor directory (dependencies) exists
 * --------------------------------------------------------------
 *
 * If the autoloader cannot be found, it means Composer
 * dependencies have not been installed yet.
 */
if (!file_exists($autoload)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit("The framework dependencies are missing. Run `composer install`.\n");
}

/**
 * --------------------------------------------------------------
 * Load Composer autoloader
 * --------------------------------------------------------------
 *
 * This enables class autoloading for the entire application,
 * including all framework and third-party packages.
 */
require_once $autoload;

/**
 * --------------------------------------------------------------
 * Environment Setup
 * --------------------------------------------------------------
 */
$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

/**
 * --------------------------------------------------------------
 * Error Reporting Configuration
 * --------------------------------------------------------------
 */
if (getenv('APP_ENV') === 'production' || getenv('APP_DEBUG') === 'false') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', dirname(__DIR__) . '/storage/logs/php_errors.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

/**
 * --------------------------------------------------------------
 * Set Default Timezone
 * --------------------------------------------------------------
 */
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');

/**
 * --------------------------------------------------------------
 * Set Custom Error Handler
 * --------------------------------------------------------------
 */
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error = new ErrorException($errstr, 0, $errno, $errfile, $errline);
    
    // Log the error - FIXED: Convert ErrorException to string
    error_log(sprintf(
        "ErrorException: %s in %s on line %d",
        $error->getMessage(),
        $error->getFile(),
        $error->getLine()
    ));
    
    if (getenv('APP_ENV') !== 'production') {
        throw $error;
    }
    
    return true;
});

/**
 * --------------------------------------------------------------
 * Set Exception Handler
 * --------------------------------------------------------------
 */
set_exception_handler(function(Throwable $exception) {
    $handler = new ExceptionHandler();
    $handler->report($exception);
    $handler->render($exception);
});

/**
 * --------------------------------------------------------------
 * Bootstrap the Careminate framework
 * --------------------------------------------------------------
 *
 * The bootstrap file returns the application instance, which
 * initializes service providers, configuration, environment
 * loading, and prepares the framework to handle requests.
 */
try {
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
    
    /**
     * --------------------------------------------------------------
     * Handle the Incoming HTTP Request
     * --------------------------------------------------------------
     *
     * Create the Request instance using PHP's global superglobals.
     * This method gives you a structured Request object that the
     * framework can pass through middleware, routing, and controllers.
     */
    $request = Request::createFromGlobals();
    
    /**
     * --------------------------------------------------------------
     *  Define the Response Content
     * --------------------------------------------------------------
     *
     * This is the raw HTML or text that will be returned to the
     * client. Later, this can come from a controller, view engine,
     * or router dispatch result.
     */
    $content = '<h1>Hello World</h1>';

    /**
     * --------------------------------------------------------------
     *  Create a Response Instance
     * --------------------------------------------------------------
     *
     * The Response class encapsulates the HTTP status code,
     * headers, and body. This ensures all output is handled
     * consistently across the entire framework.
     *
     * Parameters:
     * - content: The body to be sent to the client
     * - status:  HTTP status code (default: 200 OK)
     * - headers: Array of custom response headers
     */
    $response = new \Careminate\Http\Responses\Response(content: $content, status: 200, headers: []);

    /**
     * --------------------------------------------------------------
     * Debug Mode
     * --------------------------------------------------------------
     *
     * In development mode, you can dump the request for debugging.
     * This should never be enabled in production.
     */

     // Uncomment for debugging
    /**
     * --------------------------------------------------------------
     *  Send the Response to the Client
     * --------------------------------------------------------------
     *
     * This method outputs headers, status code, and content in the
     * correct HTTP order. After calling send(), no further output
     * should be written.
     */
    $response->send();

    // if (getenv('APP_DEBUG') === 'true' && getenv('APP_ENV') !== 'production') {
       
    // }
    
    /**
     * --------------------------------------------------------------
     * Process the Request
     * --------------------------------------------------------------
     *
     * The application handles the request through middleware,
     * routing, and returns a response.
     */
    $response = $app->handle($request);
    
    /**
     * --------------------------------------------------------------
     * Send Response
     * --------------------------------------------------------------
     */
    $response->send();
    
} catch (Throwable $e) {
    /**
     * --------------------------------------------------------------
     * Emergency Error Handling
     * --------------------------------------------------------------
     *
     * This catch block handles any errors that occur during
     * the bootstrap or request handling process.
     */
    http_response_code(500);
    
    // if (getenv('APP_ENV') !== 'production' && getenv('APP_DEBUG') === 'true') {
    //     // Development error display
    //     header('Content-Type: text/html; charset=utf-8');
    //     echo '<!DOCTYPE html>';
    //     echo '<html><head><title>Application Error</title></head><body>';
    //     echo '<h1>Application Error</h1>';
    //     echo '<h2>' . htmlspecialchars($e->getMessage()) . '</h2>';
    //     echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    //     echo '</body></html>';
    // } else {
    //     // Production error display
    //     header('Content-Type: text/plain; charset=utf-8');
    //     echo '500 Internal Server Error';
        
    //     // Log the error - FIXED: Proper string concatenation
    //     error_log(sprintf(
    //         'Unhandled exception: %s in %s on line %d' . PHP_EOL . 'Trace:' . PHP_EOL . '%s',
    //         $e->getMessage(),
    //         $e->getFile(),
    //         $e->getLine(),
    //         $e->getTraceAsString()
    //     ));
    // }
    
    // exit(1);
}

/**
 * --------------------------------------------------------------
 * End of Framework Bootstrap
 * --------------------------------------------------------------
 */

 