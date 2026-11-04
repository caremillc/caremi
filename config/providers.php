<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\DatabaseServiceProvider;

return [
    'classes' => [
        AppServiceProvider::class,
        DatabaseServiceProvider::class,
    ],
];
