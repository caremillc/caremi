<?php

return [
    'default' => env('LOG_CHANNEL', 'single'),
    'display_errors' => env('APP_DEBUG', false),
    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => storage_path(env('LOG_PATH', 'logs/error.log')),
        ],
    ],
];