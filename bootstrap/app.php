<?php

declare(strict_types=1);

use Careminate\Foundation\Application;
use Careminate\Foundation\ProviderRepository;
use Careminate\Foundation\Providers\ConfigServiceProvider;
use Careminate\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__));

$app = new Application(dirname(__DIR__));

$app->register(ConfigServiceProvider::class);

$app->setEnvironment((string) config('app.env', 'production'));

(new ProviderRepository($app))->load(config('app.providers', []));

$app->boot();

$app->markAsBootstrapped();

return $app;