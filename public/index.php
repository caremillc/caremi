<?php declare(strict_types=1);

use Dotenv\Dotenv;
use Careminate\Application;
use League\Container\Container;
use Careminate\Http\Requests\Request;
use App\Providers\LocaleServiceProvider;
use Careminate\ServiceProviders\LocaleService;

define('CAREMI_START', microtime(true));
define('BASE_PATH', dirname(__DIR__));
define('ROOT_PATH', __DIR__);
define('ROOT_DIR', __DIR__);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

 // Start capturing the current timestamp to calculate processing time
 $requestStartTime = microtime(true);

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

// request received
$request = Request::createFromGlobals();



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

// Calculate the total processing time
$requestEndTime = microtime(true);
$executionTime = $requestEndTime - $requestStartTime;

// Log the execution time (for monitoring purposes)
echo "<pre>";
echo "Request processed in " . number_format($executionTime, 4) . " seconds.\n";

// Debug: Dump the request
echo "<pre>";
dd($request);