<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Careminate\Contracts\Http\RequestInterface;
use Closure;

class TrustProxies
{
    public function handle(RequestInterface $request, Closure $next): mixed
    {
        return $next($request);
    }
}