<?php
namespace App\Http\Middlewares;

use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

class Authenticate
{
    public function handle(Request $request, callable $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return new Response('Unauthorized', 401);
        }
        
        return $next($request);
    }
}