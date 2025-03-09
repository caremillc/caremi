<?php
namespace App\Http;

use Careminate\Http\HttpKernel;

class Kernel extends HttpKernel
{
    public static $globalWeb = [
        \Careminate\Sessions\Session::class,
        \Careminate\Http\Csrf\CsrfToken::class,  // CSRF protection applied globally
    ];

    public static $middlewareWebRoute = [
        'middleware' => \App\Http\Middlewares\Middleware::class,
        'admin' => \App\Http\Middlewares\AdminMiddleware::class,
    ];

    public static $middlewareApiRoute = [];
    public static $globalApi = [];
}
