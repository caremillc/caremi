<?php

namespace App\Http;

class Kernel 
{
    public static $globalWeb = [
        \Careminate\Sessions\Session::class,
    ];

    public static $middlewareWebRoute = [
        'middleware' => \App\Http\Middlewares\Middleware::class,
        'admin'      => \App\Http\Middlewares\AdminMiddleware::class,
    ];

    public static $middlewareApiRoute = [];

    public static $globalApi = [];
}