<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\ReportServiceProvider;
use Careminate\Foundation\Application;
use Careminate\Foundation\ProviderRepository;
use Careminate\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__));

Env::require([
    'APP_ENV',
    'APP_NAME',
]);

$app = new Application(dirname(__DIR__));

$app->setEnvironment((string) env('APP_ENV', 'production'));

(new ProviderRepository($app))->load([
    AppServiceProvider::class,
    ReportServiceProvider::class,
]);

$app->boot();

$app->markAsBootstrapped();

return $app;