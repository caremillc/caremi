<?php 
namespace App\Http\Middlewares;

use Careminate\Http\Middlewares\Contracts\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{    
    /**
     * handle
     *
     * @param  mixed $request
     * @param  mixed $next
     * @param  mixed $role
     * @return void
     */
    public function handle($request, $next, ...$role)
    {
        // dd($request);
        return $next($request);  // Add custom logic for role validation if needed
    }
}