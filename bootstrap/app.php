<?php declare(strict_types=1);

use Careminate\Foundation\Application\Application;

/* ==============================
Purpose:

1.Build the Application instance
2.Load environment variables
3.Initialize the DI container
4.Register service providers
5.Load configuration
6.Register routes
7.Boot services
*/

// Define application constants
define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);

// Composer autoload
require_once BASE_PATH . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Create Application Instance
|--------------------------------------------------------------------------
*/

$app = new Application(BASE_PATH);

/*
|--------------------------------------------------------------------------
| Load Environment Variables
|--------------------------------------------------------------------------
*/

// $env = new EnvLoader(BASE_PATH);
// $env->load();

/*
|--------------------------------------------------------------------------
| Initialize Container
|--------------------------------------------------------------------------
*/

$app->initializeContainer();


/*
|--------------------------------------------------------------------------
| Load Configuration
|--------------------------------------------------------------------------
*/

// $app->loadConfiguration();

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
*/

// $app->registerConfiguredProviders();

/*
|--------------------------------------------------------------------------
| Register Routes
|--------------------------------------------------------------------------
*/

// $app->loadRoutes();

/*
|--------------------------------------------------------------------------
| Boot Services
|--------------------------------------------------------------------------
*/

$app->boot();

/*
|--------------------------------------------------------------------------
| Return Application
|--------------------------------------------------------------------------
*/
dump($app);
return $app;


