<?php // config/container.php

$container = new \League\Container\Container();

// Enable auto-resolution of dependencies through reflection
$container->delegate(new \League\Container\ReflectionContainer(true));

#parameters
// Load application routes from an external configuration file.
$routes = include BASE_PATH . '/routes/web.php';

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