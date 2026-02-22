<?php
return [
    'session_save_path'  => base_path('storage/sessions'),
    'expiration_timeout' => 86400,

    'encryption_mode'    => config('app.cipher'),
    'encryption_key'     => config('app.key'),

    'session_driver'     => env('SESSION_DRIVER', 'file'), //file|database
    'session_prefix'     => env('SESSION_PREFIX', 'caremi_'),
];
