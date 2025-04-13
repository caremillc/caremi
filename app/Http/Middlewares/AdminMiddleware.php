<?php 
namespace App\Http\Middlewares;

use Careminate\Http\Middlewares\Contracts\MiddlewareInterface;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle($request, $next, ...$roles)
    {
        // Check if 'admin' role exists in roles array
        foreach ($roles as $role) {
            if ($role === 'admin') {
                return redirect('admin/dashboard'); // Redirect to the admin dashboard
            }
        }

        return $next($request);
    }
}