<?php declare (strict_types = 1);

use Careminate\Http\Requests\Request;

use Careminate\Http\Responses\JsonResponse;
use Careminate\Http\Responses\Response;

require_once __DIR__ . '/../bootstrap/app.php';

// 1. Create request from globals
$request = Request::createFromGlobals();

// 2. Route based on request path/method
switch ($request->getPathInfo()) {
    case '/':
        // HTML response
        Response::html('<h1>Welcome</h1>')->send();
        break;

    case '/api/users':
        if ($request->isMethod('GET')) {
            // JSON response with data
            $users = [['id' => 1, 'name' => 'John']];
            JsonResponse::create($users)->send();
        } elseif ($request->isMethod('POST')) {
            // Handle POST data
            $data = $request->getJson() ?? $request->$postParams();
            // Process data...
            JsonResponse::create(['success' => true])->send();
        }
        break;

    case '/contact':
        if ($request->isMethod('POST')) {
            // Form handling
            $name  = $request->post('name', 'Anonymous');
            $email = $request->post('email');

            // Validate input
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Response::html('Invalid email', 400)->send();
                exit;
            }

            // Process form (e.g., send email)
            Response::redirect('/thank-you')->send();
        } else {
            Response::html('<form method="POST">...</form>')->send();
        }
        break;

    default:
        Response::notFound('Page not found')->send();
}
