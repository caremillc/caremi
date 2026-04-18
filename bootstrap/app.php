<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Careminate\Foundation\Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Register core providers
|--------------------------------------------------------------------------
|
| Later we will register ConfigServiceProvider, RoutingServiceProvider,
| ViewServiceProvider, DatabaseServiceProvider, etc.
|
*/

$app->register(\Careminate\Foundation\Providers\ConfigServiceProvider::class);  // this code
$app->register(\Careminate\Foundation\Providers\HttpServiceProvider::class);
$app->boot();

return $app;