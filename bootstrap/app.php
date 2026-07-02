<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\ReportServiceProvider;
use Careminate\Foundation\Application;
use Careminate\Foundation\ProviderRepository;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application(dirname(__DIR__));

$app->setEnvironment($_ENV['APP_ENV'] ?? 'local');

(new ProviderRepository($app))->load([
    AppServiceProvider::class,
    ReportServiceProvider::class,
]);

$app->boot();

$app->markAsBootstrapped();

return $app;