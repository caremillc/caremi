<?php declare (strict_types = 1);

use Careminate\Database\Dbal\Connections\ConnectionFactory;
use Careminate\Database\Dbal\Connections\Contracts\ConnectionInterface;
use Careminate\Database\Dbal\Connections\DatabaseManager;
use Careminate\Exceptions\Handler;
use Careminate\Exceptions\HandlerInterface;
use Careminate\Http\Kernel;
use Careminate\Routing\Router;
use Careminate\Routing\RouterInterface;
use Doctrine\DBAL\Connection;

// Load environment variables
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');
/**
 * --------------------------------------------------------------------------
 * Service Container Configuration
 * --------------------------------------------------------------------------
 *
 * This file configures the service container for the Careminate framework.
 * It sets up bindings and dependencies using the League\Container.
 *
 * Services registered:
 * - Binds the RouterInterface to the Router concrete implementation.
 * - Registers the HTTP Kernel with the RouterInterface dependency.
 *
 * @package Careminate\Framework
 */

$container = new \League\Container\Container();

// Enable auto-resolution of dependencies through reflection
$container->delegate(new \League\Container\ReflectionContainer(true));

#parameters
// Load application routes from an external configuration file.
$routes = include BASE_PATH . '/routes/web.php';
# twig template path
$templatesPath = BASE_PATH . '/templates/views';
#env parameters
$appEnv     = env('APP_ENV', 'production'); // Default to 'production' if not set
$appKey     = env('APP_KEY');               // Default to 'production' if not set
$appVersion = env('APP_VERSION');

$container->add('APP_ENV', new \League\Container\Argument\Literal\StringArgument($appEnv));
$container->add('APP_KEY', new \League\Container\Argument\Literal\StringArgument($appKey));
$container->add('APP_VERSION', new \League\Container\Argument\Literal\StringArgument($appVersion));

// Bind RouterInterface to Router implementation
$container->add(RouterInterface::class, Router::class)
    ->setShared(true);

// Register interfaces to their implementations
$container->add(HandlerInterface::class, Handler::class)
    ->setShared(true);

    
    // Register the RequestHandler service which implements the RequestHandlerInterface.
// This handler is responsible for processing the middleware stack.
$container->add(
    \Careminate\Http\Middlewares\Contracts\RequestHandlerInterface::class,
    \Careminate\Http\Middlewares\RequestHandler::class
);

// Register kernel with explicit arguments using references to other services
$container->add(Kernel::class)
    ->addArgument(RouterInterface::class)
    ->addArgument(HandlerInterface::class) // Use the interface, not the concrete class
    ->addArgument(\Careminate\Http\Middlewares\Contracts\RequestHandlerInterface::class) // Middleware pipeline handler
    ->addArgument($container)
    ->setShared(true);

// Extend RouterInterface definition to inject routes
$container->extend(Careminate\Routing\RouterInterface::class)
    ->addMethodCall('setRoutes', [new League\Container\Argument\Literal\ArrayArgument($routes)]);

// Register the Twig FilesystemLoader as a shared (singleton) service.
// It will use the provided $templatesPath as the base directory for template files.
$container->add('template-renderer-factory', \Careminate\Template\TwigFactory::class)
    ->addArguments([
        \Careminate\Sessions\SessionInterface::class,                          // Inject session service
        new \League\Container\Argument\Literal\StringArgument($templatesPath), // Path to view templates
    ]);

$container->addShared('twig', function () use ($container) {
    return $container
        ->get('template-renderer-factory')
        ->create();
});
// Register the AbstractController so it can be resolved by the container.
$container->add(\Careminate\Http\Controllers\AbstractController::class);

// Automatically call the setContainer() method on any class that extends AbstractController
// This injects the container itself into the controller, enabling dependency resolution within controllers.
$container->inflector(\Careminate\Http\Controllers\AbstractController::class)
    ->invokeMethod('setContainer', [$container]);

# start database connection
$dbConfig = require BASE_PATH . '/config/database.php';

$defaultDriver = $dbConfig['default'];

if (! isset($dbConfig['drivers'][$defaultDriver])) {
    throw new \RuntimeException("Database driver [$defaultDriver] not supported.");
}

$driverConfig = $dbConfig['drivers'][$defaultDriver];

/**
 * Register ConnectionFactory
 */
$container->addShared(ConnectionInterface::class, ConnectionFactory::class)
    ->addArgument($driverConfig);

/**
 * Register Doctrine DBAL Connection as Singleton
 */
$container->addShared(Connection::class, function () use ($container) {
    return $container->get(ConnectionInterface::class)->create();
});

$container->addShared(DatabaseManager::class);
# end database connection

//add session to container
$container->addShared(Careminate\Sessions\SessionInterface::class, Careminate\Sessions\Session::class
);

// Debug output (should be removed in production)
// dd($container);

return $container;
