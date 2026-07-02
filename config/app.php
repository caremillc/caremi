<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Careminate'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),

    'providers' => [
        App\Providers\AppServiceProvider::class,
        App\Providers\AppServiceProvider::class,
   
    ],
];