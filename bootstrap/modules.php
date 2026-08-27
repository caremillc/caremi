<?php

declare(strict_types=1);

use App\Billing\BillingModule;
use App\Users\UsersModule;
use Careminate\Module\Cache\ModuleCache;
use Careminate\Module\Discovery\ComposerModuleDiscovery;
use Careminate\Module\Discovery\LocalModuleDiscovery;
use Careminate\Module\ModuleManager;

$basePath = dirname(__DIR__);
$cachePath = $basePath . '/bootstrap/cache/modules.json';

return (new ModuleManager())
    ->discoverUsing(new LocalModuleDiscovery([
        UsersModule::class,
        BillingModule::class,
    ]))
    ->discoverUsing(
        ComposerModuleDiscovery::fromVendorDirectory(
            $basePath . '/vendor',
        ),
    )
    ->useCachedPlan((new ModuleCache())->load($cachePath));