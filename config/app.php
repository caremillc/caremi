<?php

declare(strict_types=1);

use Careminate\Config\Environment;

/**
 * @var Environment $environment
 */
return [
    'name'        => $environment->required('APP_NAME'),
    'environment' => $environment->required('APP_ENV'),
    'debug'       => $environment->boolean('APP_DEBUG'),
    'port'        => $environment->integer('APP_PORT'),
];
