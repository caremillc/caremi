<?php declare(strict_types=1);

use Careminate\Application;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

define('APP_START', microtime(true));

require BASE_PATH . '/bootstrap/app.php';

try {
    $request = Request::capture();
    
    $app = new Application();
    $response = $app->handle($request);

    $response->send();
    $app->terminate($request, $response);
    
} catch (Throwable $e) {
    // Log the error for debugging
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
    
    (new Response('Internal Server Error: ' . $e->getMessage(), 500))->send();
}
