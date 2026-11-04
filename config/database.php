<?php

declare (strict_types=1);

use Careminate\Config\Environment;

/** @var Environment $environment */
return [
    'default'     => $environment->get('DB_CONNECTION', 'mysql') ?? 'mysql',

    'connections' => [
        'mysql' => [
            'driver'          => 'mysql',
            'host'            => $environment->get('DB_HOST', '127.0.0.1', ) ?? '127.0.0.1',
            'port'            => $environment->integer('DB_PORT', 3306, ),
            'database'        => $environment->get('DB_DATABASE', 'careminate', ) ?? 'careminate',
            'username'        => $environment->get('DB_USERNAME', 'careminate', ) ?? 'careminate',
            'password'        => $environment->get('DB_PASSWORD', '', ) ?? '',
            'connect_timeout' => $environment->integer('DB_TIMEOUT', 3, ),
        ],
    ],
];
