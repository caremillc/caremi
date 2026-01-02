<?php declare(strict_types=1);

use Careminate\Http\Kernel;
use Careminate\Exceptions\ExceptionHandler;
use Careminate\Http\Requests\Request;
use Careminate\Exceptions\Http\AuthException;

// ---------------------------------------------------------
// Bootstrap the framework
// ---------------------------------------------------------
require __DIR__ . '/../bootstrap/app.php';

try {
    // ---------------------------------------------------------
    // Capture the current HTTP request
    // ---------------------------------------------------------
    $request = Request::createFromGlobals();

    // ---------------------------------------------------------
    // Pass the request to the Kernel for handling
    // ---------------------------------------------------------
    $kernel = new Kernel();
    $response = $kernel->handle($request);

    // ---------------------------------------------------------
    // Send the HTTP response back to the client
    // ---------------------------------------------------------
    $response->send();

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
    if (getenv('APP_DEBUG') === 'true' || getenv('APP_DEBUG') === '1') {
        // Dev mode: show full stack trace for debugging
        echo "<pre>" . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . "</pre>";
        exit;
    }

    // Production mode: render friendly error page
    $handler = new ExceptionHandler();
    $handler->render($e, $request)->send();
}

