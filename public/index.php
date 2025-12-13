<?php declare(strict_types=1);

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
 *  Handle the Incoming HTTP Request
 * --------------------------------------------------------------
 *
 * Here we create the Request instance using PHP's global
 * superglobals ($_GET, $_POST, $_SERVER, etc.). This method
 * gives you a structured Request object that the framework
 * can pass through middleware, routing, and controllers.
 */
$request = \Careminate\Http\Requests\Request::createFromGlobals();

/**
 * --------------------------------------------------------------
 *  Debug: Dump the Request Object
 * --------------------------------------------------------------
 *
 * `dd()` is a temporary debugging helper used to inspect the
 * Request object. This stops execution and prints formatted
 * information useful during the development stage.
 */


// URL: /test?name=John
$name = $request->query('name'); // "John"

// POST: name=Jane
$name = $request->getParam('name'); // "Jane"

// Either GET or POST
$name = $request->getParam('name'); // POST has priority

// dd($name);
dd($request);

// AuthMiddleware example
// if ($request->getParam('auth') !== '1') {
//     return new Response('<h1>401 Unauthorized</h1>', 401);
// }