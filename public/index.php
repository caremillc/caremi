<?php declare(strict_types=1);

use Careminate\Supports\Str;
use Careminate\Supports\Config;

/**
 * --------------------------------------------------------------
 *  Define the path to Composer's autoloader
 * --------------------------------------------------------------
 */
$autoload = dirname(__DIR__) . '/vendor/autoload.php';

/**
 * --------------------------------------------------------------
 *  Ensure the vendor directory (dependencies) exists
 * --------------------------------------------------------------
 *
 * If the autoloader cannot be found, it means Composer
 * dependencies have not been installed yet.
 */
if (! file_exists($autoload)) {
    http_response_code(500);
    exit("The framework dependencies are missing. Run `composer install`.");
}

/**
 * --------------------------------------------------------------
 *  Load Composer autoloader
 * --------------------------------------------------------------
 *
 * This enables class autoloading for the entire application,
 * including all framework and third-party packages.
 */
require_once $autoload;

/**
 * --------------------------------------------------------------
 *  Bootstrap the Careminate framework
 * --------------------------------------------------------------
 *
 * The bootstrap file returns the application instance, which
 * initializes service providers, configuration, environment
 * loading, and prepares the framework to handle requests.
 */
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

/**
 * --------------------------------------------------------------
 *  TEMPORARY RESPONSE FOR TESTING
 * --------------------------------------------------------------
 *
 * Replace this with your request handling once the kernel,
 * router, and response system are fully wired.
 */
$name = Config::get('app.name');
dd($name);
