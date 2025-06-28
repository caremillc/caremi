<?php 
namespace App\Http\Middlewares;

use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

class AuthMiddleware
{
    public function handle(Request $request): Response|string|null
    {
        if (!$request->session()->get('auth_id')) {
            return new Response('Unauthorized', 401);
        }

        return null; // Continue to controller
    }
}
