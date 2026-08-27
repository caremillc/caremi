<?php

declare(strict_types=1);

use Careminate\Application\ApplicationBuilder;
use Careminate\Module\Cache\ModuleCache;
use Careminate\Module\ModuleBootstrapper;
use Careminate\Module\ModuleManager;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var ModuleManager $modules */
$modules = require dirname(__DIR__) . '/bootstrap/modules.php';

$application = ApplicationBuilder::fromBasePath(dirname(__DIR__))
    ->bootstrapper(
        new ModuleBootstrapper($modules),
        priority: -100,
    )
    ->build();

$application->bootstrap();

$path = dirname(__DIR__) . '/bootstrap/cache/modules.json';

(new ModuleCache())->write($path, $modules->plan());

fwrite(STDOUT, sprintf(
    "Module cache written to %s%s",
    $path,
    PHP_EOL,
));