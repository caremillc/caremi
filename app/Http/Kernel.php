<?php declare (strict_types = 1);

namespace App\Http;

class Kernel
{
    public static $globalWeb = [
        // \Careminate\Session\Session::class,
    ];

    public static $middlewareWebRoute = [];

    public static $middlewareApiRoute = [];

    public static $globalApi = [];
}
