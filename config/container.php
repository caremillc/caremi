<?php declare(strict_types=1);

// Load environment variables
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');

$container = new \League\Container\Container();

// Enable auto-resolution of dependencies through reflection
$container->delegate(new \League\Container\ReflectionContainer(true));

#parameters
// Load application routes from an external configuration file.
$routes = include BASE_PATH . '/routes/web.php';

#env parameters
$appEnv = env('APP_ENV', 'production'); // Default to 'production' if not set
$appKey = env('APP_KEY', ''); // Default to 'production' if not set
$appVersion = env('APP_VERSION', '');

$container->add('APP_ENV', new \League\Container\Argument\Literal\StringArgument($appEnv));
$container->add('APP_KEY', new \League\Container\Argument\Literal\StringArgument($appKey));
$container->add('APP_VERSION', new \League\Container\Argument\Literal\StringArgument($appVersion));

# services
# add alias for Router class,
$container->add(\Careminate\Routing\RouterInterface::class, \Careminate\Routing\Router::class);

// Extend RouterInterface definition to inject routes
$container->extend(Careminate\Routing\RouterInterface::class)
          ->addMethodCall('setRoutes', [new League\Container\Argument\Literal\ArrayArgument($routes)]);

// $container->add(Careminate\Http\HttpKernel::class)
//           ->addArgument(Careminate\Routing\RouterInterface::class);
$container->add(Careminate\Http\Kernel::class)
          ->addArgument(Careminate\Routing\RouterInterface::class)
          ->addArgument($container);

// dd($container);
return $container;