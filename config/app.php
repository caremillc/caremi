<?php declare (strict_types = 1);

return [
    'name'            => env('APP_NAME') ?? 'Careminate',
    'env'             => env('APP_ENV') ?? 'production',
    'debug'           => env('APP_DEBUG'),
    'url'             => env('APP_URL') ?? 'http://localhost',
    'timezone'        => env('APP_TIMEZONE', 'UTC'),
    'key'             => 'frm_phpanonymous',
    // Application locales
    'locale'          => 'en',
    'fallback_locale' => 'en',

    // Encryption
    'key'             => env('APP_KEY', ''),
    'cipher'          => 'AES-256-CBC',
];
