<?php // config/container.php
// Load environment variables
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');

$container = new \League\Container\Container();

// Enable auto-resolution of dependencies through reflection
$container->delegate(new \League\Container\ReflectionContainer(true));

#parameters
// Load application routes from an external configuration file.
$routes = include BASE_PATH . '/routes/web.php';
$templatesPath = BASE_PATH . '/templates';
#sqlite path
$databaseUrl = 'sqlite:///' . BASE_PATH . '/database/database.sqlite';

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

$container->addShared('filesystem-loader', \Twig\Loader\FilesystemLoader::class)
    ->addArgument(new \League\Container\Argument\Literal\StringArgument($templatesPath));

// twig alias    
$container->addShared('twig', \Twig\Environment::class)
          ->addArgument('filesystem-loader');
		  
//add AbstractController      
$container->add(Careminate\Http\Controllers\AbstractController::class);

//setContainer
$container->inflector(Careminate\Http\Controllers\AbstractController::class)
          ->invokeMethod('setContainer', [$container]);

# dbal connection 
$container->add(Careminate\Databases\Dbal\ConnectionFactory::class)
->addArguments([
    new \League\Container\Argument\Literal\StringArgument($databaseUrl)
]);

$container->addShared(\Doctrine\DBAL\Connection::class, function () use ($container): \Doctrine\DBAL\Connection {
return $container->get(Careminate\Databases\Dbal\ConnectionFactory::class)->create();
});

//  dd($container);
return $container;
