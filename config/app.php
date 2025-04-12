<?php

return [
    'name'             => env('APP_NAME', 'Caremi'),
    'debug'            => env('APP_DEBUG', false),
    'key'              => env('APP_KEY', ''),
    'locale'           => env('APP_LOCALE', 'ar'),
    'fallback_locale'  => env('APP_FALLBACK_LOCALE', 'en'),
    'timezone'         => env('APP_TIMEZONE', 'Asia/Dubai'),
    'fallback_timezone'=> 'GMT',
    'cipher'           => 'AES-256-CBC',
    'sha_algorithm'    => 'sha256',
    'features'         => env('ENABLED_FEATURES', ['basic']),
];
