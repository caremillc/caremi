<?php

return [
    'aliases' => [
        'auth'    => App\Http\Middlewares\AuthMiddleware::class,
        'guest'   => App\Http\Middlewares\GuestMiddleware::class,
        'admin'   => App\Http\Middlewares\AdminMiddleware::class,
        'throttle' => App\Http\Middlewares\ThrottleRequests::class,
    ],

    // Optional middleware groups (step 2)
    'groups' => [
        'web' => ['auth', 'csrf'],
        'api' => ['throttle'],
    ],
];
