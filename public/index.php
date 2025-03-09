<?php declare(strict_types=1);

use Dotenv\Dotenv;
use Careminate\Application;
use League\Container\Container;
use App\Providers\LocaleServiceProvider;
use Careminate\ServiceProviders\LocaleService;


define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);
define('ROOT_DIR', __DIR__);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Initialize the HttpKernel to check PHP version and maintenance mode
// $kernel = new Careminate\Http\HttpKernel();

// Initialize the service container
$container = new Container();

// Register the LocaleServiceProvider with the container
$localeServiceProvider = new LocaleServiceProvider($container);
$localeServiceProvider->register();

// Registering Application as a service in the container
$container->add(Application::class, function () use ($container) {
    // Inject LocaleService from the container into the Application class
    return new Application($container->get(LocaleService::class));
});

try {
    // Initialize the application
    $app = $container->get(Application::class);
    $app->start();

       // Log current request URI
      // var_dump('Request URI: ' . $_SERVER['REQUEST_URI']);

} catch (RuntimeException $e) {
    // Handle any exceptions thrown during application startup
    http_response_code(500);
    echo $e->getMessage();
}
