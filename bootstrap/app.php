<?php declare(strict_types=1);

use Careminate\Foundation\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__));

/*
|--------------------------------------------------------------------------
| Register core providers
|--------------------------------------------------------------------------
|
| Later we will register ConfigServiceProvider, RoutingServiceProvider,
| ViewServiceProvider, DatabaseServiceProvider, etc.
|
*/

// $app->register(\Careminate\Providers\ConfigServiceProvider::class);

$app->boot();

return $app;