<?php 

use App\Providers\DebugServiceProvider;
use Careminate\Providers\AnalyticsServiceProvider;

return [
    //Register the Provider
    \Careminate\Providers\EnvironmentServiceProvider::class,

    //Register the Provider
     \Careminate\Providers\EncryptionServiceProvider::class,

    env('APP_ENV') === 'production'
        ? AnalyticsServiceProvider::class
        : DebugServiceProvider::class,
];