<?php declare(strict_types=1);

use Careminate\Exceptions\Handler;
use Careminate\Exceptions\HandlerInterface;
use Careminate\Http\Kernel;
use Careminate\Routing\Router;
use Careminate\Routing\RouterInterface;

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

#parameters
// Load application routes from an external configuration file.
$routes = include BASE_PATH . '/routes/web.php';

// Bind RouterInterface to Router implementation
$container->add(RouterInterface::class, Router::class)
          ->setShared(true);

// Register interfaces to their implementations
$container->add(HandlerInterface::class, Handler::class)
          ->setShared(true);

// Register router as singleton
// $container->add(Router::class)->setShared(true);

// Register handler as singleton
// $container->add(Handler::class)->setShared(true);

// Register the HTTP Kernel with its dependencies
// Register kernel with explicit arguments using references to other services
$container->add(Kernel::class)
          ->addArgument(RouterInterface::class)
          ->addArgument(HandlerInterface::class) // Use the interface, not the concrete class
          ->setShared(true);

          // Extend RouterInterface definition to inject routes
$container->extend(Careminate\Routing\RouterInterface::class)
          ->addMethodCall('setRoutes',[new League\Container\Argument\Literal\ArrayArgument($routes)]);

// Debug output (should be removed in production)
// dd($container);

return $container;