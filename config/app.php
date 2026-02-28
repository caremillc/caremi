<?php declare (strict_types = 1);

return [
    'name'            => env('APP_NAME', 'Caremi'),
    'version'         => env('APP_VERSION', '1.0.0'), // this
    'env'             => env('APP_ENV', 'production'),
    'debug'           => (bool) env('APP_DEBUG', false), # Whether to enable debug mode (true/false)
    'url'             => env('APP_URL', 'http://localhost:8000'),
    'asset_url'       => env('ASSET_URL'),
    'timezone'        => 'UTC',
    'locale'          => 'en',
    'fallback_locale' => 'en',
    'faker_locale'    => 'en_US',
    'key'             => env('APP_KEY'),
    'cipher'          => 'AES-256-CBC',
    'maintenance'     => [
        'driver' => 'file',
    ],


    'providers' => [
        \Careminate\Providers\EventServiceProvider::class,
    ],
    
];
