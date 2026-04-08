<?php

use Careminate\Foundation\Application\Application;
use Careminate\Http\HttpKernel;
use Careminate\Http\Requests\Request;

define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(dirname(__DIR__));

// $app->registerBaseBindings(); 
// $app->registerBaseServiceProviders(); 
// $app->registerServiceProviders(); 

/*
|--------------------------------------------------------------------------
| Register Providers
|--------------------------------------------------------------------------
*/

$app->registerConfiguredProviders();

/*
|--------------------------------------------------------------------------
| Handle Request
|--------------------------------------------------------------------------
*/

$request = $app->make(Request::class);

$kernel = $app->make(HttpKernel::class);

$kernel->send($request);