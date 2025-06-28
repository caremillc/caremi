<?php 
return [
    'aliases' => [
        'auth'    => App\Http\Middlewares\AuthMiddleware::class,
        'encrypt' => App\Http\Middlewares\EncryptCookies::class,
        'session' => App\Http\Middlewares\StartSession::class,
    ],

    'groups' => [
        'web' => ['encrypt', 'session', 'auth'],
    ],

    'priority' => [
        App\Http\Middleware\EncryptCookies::class,
        App\Http\Middleware\StartSession::class,
        App\Http\Middleware\AuthMiddleware::class,
    ],
];
