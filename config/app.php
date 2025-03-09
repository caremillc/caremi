<?php 
return [
    'name'             => env('APP_NAME', 'caremi'),           // The name of the application, defaults to 'caremi'
    'key'              => env('APP_KEY'),                      // The encryption key, must be set in .env
    'debug'            => env('APP_DEBUG', false),             // Enable or disable debugging, default is false
    'features'         => env('ENABLED_FEATURES', ['basic']),  // List of enabled features (supports JSON array)
    'locale'           => env('APP_LOCALE', 'en'),             // The default locale for the application
    'fallback_locale'  => env('APP_FALLBACK_LOCALE', 'en'),    // Fallback locale if the default locale is unavailable
    'timezone'         => env('APP_TIMEZONE'),                 // The default timezone, fetched from .env
    'fallback_timezone'=> 'GMT',                               // Fallback timezone, default is GMT if not set
    'sha_algorithm'    => 'sha256',                            // Default SHA algorithm for hashing
    'cipher'           => 'AES-256-CBC',                       // Default cipher for encryption
];