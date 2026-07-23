<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use Careminate\Contracts\Application\ApplicationInterface;
use Careminate\Foundation\Application;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = new Application(dirname(__DIR__));
$app->register(AppServiceProvider::class);

/** @var ApplicationInterface $app */
return $app;