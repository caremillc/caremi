<?php

return [
    'driver'  => env('DB_DRIVER', 'sqlite'),

    'drivers' => [
        'sqlite' => [
            'engine'     => 'sqlite',
            'path'       => base_path('database/database.sqlite'),
            'FETCH_MODE' => PDO::FETCH_ASSOC,
            'ERRMODE'    => PDO::ATTR_ERRMODE,
            'EXCEPTION'  => PDO::ERRMODE_EXCEPTION,
        ],

        'mysql'  => [
            'engine'     => 'mysql',
            'host'       => env('DB_HOST', '127.0.0.1'),
            'port'       => env('DB_PORT', '3306'),
            'database'   => env('DB_DATABASE', 'forge'),
            'username'   => env('DB_USERNAME', 'root'),
            'password'   => env('DB_PASSWORD', ''),
            'charset'    => 'utf8mb4',
            'FETCH_MODE' => PDO::FETCH_OBJ, //  PDO::FETCH_ASSOC,
            'ERRMODE'    => PDO::ATTR_ERRMODE,
            'EXCEPTION'  => PDO::ERRMODE_EXCEPTION,
            'strict'     => true,
        ],

    ],
];