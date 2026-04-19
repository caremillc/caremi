<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Careminate\Contracts\Http\RequestInterface;
use Closure;

class AuthMiddleware
{
    public function handle(RequestInterface $request, Closure $next): mixed
    {
        $authenticated = $request->query('auth', '0') === '1';

        if (! $authenticated) {
            return redirect('/login');
        }

        return $next($request);
    }
}

// class AuthMiddleware
// {
//     public function handle(RequestInterface $request, Closure $next): mixed
//     {
//         if (! $authenticated) {
//             return redirect(route('login'));
//         }

//         return $next($request);
//     }
// }