<?php declare(strict_types=1);

return [

    'name' => 'Careminate',

    'env' => 'local',

    'providers' => [
        Careminate\Providers\HttpServiceProvider::class,
        Careminate\Providers\RoutingServiceProvider::class,
        // Careminate\Providers\ViewServiceProvider::class,
        // Careminate\Providers\DatabaseServiceProvider::class,

    ],

];