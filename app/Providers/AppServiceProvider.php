<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ApplicationIdentity;
use Careminate\Config\Configuration;
use Careminate\Container\Container;
use Careminate\Foundation\ServiceProvider;

final class AppServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $configuration = $container->get(Configuration::class);

        if (!$configuration instanceof Configuration) {
            throw new \LogicException(
                'Application configuration is not available.'
            );
        }

        $container->instance(
            ApplicationIdentity::class,
            new ApplicationIdentity(
                $configuration->string('app.name'),
                $configuration->string('app.environment')
            )
        );
    }

    public function boot(Container $container): void
    {
        $identity = $container->get(ApplicationIdentity::class);

        if (!$identity instanceof ApplicationIdentity) {
            throw new \LogicException(
                'Application identity service is not available.'
            );
        }
    }
}
