<?php

declare(strict_types=1);

use Careminate\Application\ApplicationBuilder;
use Careminate\Module\ModuleBootstrapper;
use Careminate\Module\ModuleManager;

require dirname(__DIR__) . '/vendor/autoload.php';

/** @var ModuleManager $modules */
$modules = require __DIR__ . '/modules.php';

return ApplicationBuilder::fromBasePath(dirname(__DIR__))
    ->bootstrapper(
        new ModuleBootstrapper($modules),
        priority: -100,
    )
    ->build();