<?php declare(strict_types=1);

use App\Models\User;
use Careminate\Http\Kernel;
use Careminate\Routing\Router;
use Careminate\Http\Requests\Request;
use Careminate\Exceptions\ExceptionHandler;
use Careminate\Exceptions\Http\AuthException;


// ---------------------------------------------------------
// Bootstrap the framework
// ---------------------------------------------------------
require __DIR__ . '/../bootstrap/app.php';
$container = require BASE_PATH . '/config/container.php';



//dd($container);
try {

    // require_once '../db_test.php';
    // ---------------------------------------------------------
    // Capture the current HTTP request
    // ---------------------------------------------------------
    // Converts PHP superglobals ($_GET, $_POST, $_SERVER, $_FILES, etc.)
    // into a normalized Request object with helpful methods.
    $request = Request::createFromGlobals();

    /*
        $request now contains:
        - $request->getParams : $_GET parameters
        - $request->postParams : $_POST parameters
        - $request->cookies : $_COOKIE
        - $request->files : $_FILES
        - $request->server : $_SERVER
        - $request->inputParams : parsed JSON or raw body input
        - $request->rawInput : raw body string (php://input)
        - Methods like:
            $request->getMethod() -> HTTP method (supports spoofing)
            $request->getPathInfo() / $request->path() -> Request URI path
            $request->header('Accept') -> Access headers
            $request->userAgent() -> Client User-Agent
            $request->isAjax() -> Detect AJAX requests
            $request->all() -> Combined GET, POST, and input params
    */

    //instantiate router
    $router = new Router();

    // ---------------------------------------------------------
    // Pass the request to the Kernel for handling
    // ---------------------------------------------------------
    //parse the $router into the constructor of the kernel class
    //$kernel = new Kernel($router);
	$kernel =  $container->get(Kernel::class);  

    /*
        The Kernel processes the request:
        - Resolves the route (in a full framework)
        - Applies middleware
        - Calls the controller or closure
        - Returns a Response object

        $response contains:
        - $response->content : HTML/JSON/string to be sent
        - $response->status : HTTP status code
        - $response->headers : HTTP headers array
        - $response->send() : sends headers and body to the client
    */
    $response = $kernel->handle($request);

    // ---------------------------------------------------------
    // Send the HTTP response back to the client
    // ---------------------------------------------------------
    $response->send();

    // ---------------------------------------------------------
    // Optional debugging: inspect request and response objects
    // ---------------------------------------------------------
    // dd($request);   // Dump Request object details
    // dd($response);  // Dump Response object details

} catch (AuthException $e) {
    // ---------------------------------------------------------
    // Handle authentication/authorization errors (401)
    // ---------------------------------------------------------
    $handler = new ExceptionHandler();
    $handler->render($e, $request)->send();

} catch (\Throwable $e) {
    // ---------------------------------------------------------
    // Handle all other uncaught exceptions
    // ---------------------------------------------------------
    if (getenv('APP_DEBUG') === 'true') {
        // Dev mode: show full stack trace for debugging
        echo "<pre>" . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . "</pre>";
        exit;
    }

    // Production mode: render friendly error page
    $handler = new ExceptionHandler();
    $handler->render($e, $request)->send();
}
