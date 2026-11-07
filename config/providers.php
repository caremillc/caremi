<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\DatabaseServiceProvider;
use App\Providers\ViewServiceProvider;

return [
    'classes' => [
        AppServiceProvider::class,
        DatabaseServiceProvider::class,
        ViewServiceProvider::class,
    ],
];
