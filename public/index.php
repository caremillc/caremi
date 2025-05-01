<?php declare(strict_types=1);
use Careminate\Http\Responses\Response;

require_once __DIR__ . '/../bootstrap/app.php';

// Example route handling
$path = $_SERVER['REQUEST_URI'] ?? '/';

switch ($path) {
    case '/':
        Response::html('<h1>Welcome</h1><p>Homepage content</p>')->send();
        break;
    
    case '/api':
        Response::json(['status' => 'success', 'data' => ['id' => 123]])->send();
        break;
        
    case '/redirect':
        Response::redirect('/new-location')->send();
        break;
        
    case '/admin':
        Response::html('Admin area', Response::HTTP_OK, ['Cache-Control' => 'no-cache'])->send();
        break;
        
    case '/404':
        Response::notFoundHtml('<h1>Custom 404 Page</h1>')->send();
        break;
        
    case '/200':
        Response::html('<h1>Success</h1>', 200, ['X-Custom-Header' => 'Value'])->send();
        break;

    default:
        Response::notFound('Page not found')->send();
}
