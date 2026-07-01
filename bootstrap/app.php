<?php

declare(strict_types=1);

use Careminate\Foundation\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application(dirname(__DIR__));

$app->setEnvironment($_ENV['APP_ENV'] ?? 'local');

$app->markAsBootstrapped();

return $app;