<?php declare(strict_types=1);

// bootstrapping
require dirname(__DIR__) . '/bootstrap/app.php';
require dirname(__DIR__) . '/bootstrap/performance.php';

$container = require BASE_PATH . '/config/container.php';

// request received
$request = \Careminate\Http\Requests\Request::createFromGlobals();

// Retrieve the EventDispatcher instance from the container and register a listener:
// - The listener `ContentLengthListener` will be triggered when the `ResponseEvent` is dispatched,
//   allowing it to automatically set the Content-Length header on the response.
$eventDispatcher = $container->get(\Careminate\Database\EventDispatcher\EventDispatcher::class);
$eventDispatcher->addListener(
    \Careminate\Http\Events\ResponseEvent::class,
    new Careminate\Database\EventListener\InternalErrorListener()
)->addListener(\Careminate\Http\Events\ResponseEvent::class,
    new Careminate\Database\EventListener\ContentLengthListener()
);

try {
    // Get kernel from container (auto-wires dependencies)
    $kernel = $container->get(\Careminate\Http\Kernel::class);
    
    // Bootstrap the application
    // $kernel->bootstrap();
    
    // handle request
    $response = $kernel->handle($request);
    
    // send response
    $response->send();
    
    // terminate
    $kernel->terminate($request, $response);
    
} catch (\Throwable $e) {
    // Fallback error handler if kernel fails
    http_response_code(500);
    echo "<h1>Fatal Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}





