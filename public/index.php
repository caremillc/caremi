<?php declare(strict_types=1);

use Careminate\Http\Kernel;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

define('BASE_PATH', dirname(__DIR__));

// ✅ Autoload first
require_once BASE_PATH . '/vendor/autoload.php';

// ✅ Load .env before bootstrap
Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

// ✅ Then bootstrap (safe to use env now)
require_once __DIR__ . '/../bootstrap/app.php';

try {
    $request = Request::capture();
    
    $kernel = new Kernel();
    $response = $kernel->handle($request);

    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // Log the error for debugging
    error_log($e->getMessage());
    error_log($e->getTraceAsString());
    
    (new Response('Internal Server Error: ' . $e->getMessage(), 500))->send();
}

?>
