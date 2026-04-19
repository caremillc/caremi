<?php declare(strict_types=1);

use App\Http\Middleware\TrustProxies;
use Careminate\Contracts\Http\KernelInterface;
use Careminate\Foundation\Application;
use Careminate\Foundation\Providers\ConfigServiceProvider;
use Careminate\Foundation\Providers\HttpServiceProvider;
use Careminate\Foundation\Providers\KernelServiceProvider;
use Careminate\Foundation\Providers\RoutingServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';


$app = new Application(dirname(__DIR__));

$app->register(ConfigServiceProvider::class);
$app->register(HttpServiceProvider::class);
$app->register(KernelServiceProvider::class);
$app->register(RoutingServiceProvider::class);

$app->boot();

/** @var KernelInterface $kernel */
$kernel = $app->make(KernelInterface::class);
$kernel->setMiddleware([
    TrustProxies::class,
]);

return $app;