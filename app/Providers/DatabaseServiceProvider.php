<?php

declare(strict_types=1);

namespace App\Providers;

use Careminate\Config\Configuration;
use Careminate\Container\Container;
use Careminate\Database\Connection;
use Careminate\Database\ConnectionDefinition;
use Careminate\Database\ConnectionManager;
use Careminate\Database\MySql\MySqlConnection;
use Careminate\Foundation\ServiceProvider;
use InvalidArgumentException;
use LogicException;

/**
 * Register connection definitions without opening a database.
 */
final class DatabaseServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $configuration = $container->get(Configuration::class);

        if (! $configuration instanceof Configuration) {
            throw new LogicException(
                'Application configuration is not available.',
            );
        }

        $definitions = [];

        foreach (
            $configuration->array('database.connections')
            as $name => $settings
        ) {
            if (
                ! is_string($name)
                || ! is_array($settings)
            ) {
                throw new InvalidArgumentException(
                    'Invalid database connection configuration.',
                );
            }

            $driver = $settings['driver'] ?? null;

            if (! is_string($driver)) {
                throw new InvalidArgumentException(
                    'Invalid database connection configuration.',
                );
            }

            /** @var array<string, int|string> $options */
            $options = [];

            foreach ($settings as $key => $value) {
                if ($key === 'driver') {
                    continue;
                }

                if (
                    ! is_string($key)
                    || (
                        ! is_string($value)
                        && ! is_int($value)
                    )
                ) {
                    throw new InvalidArgumentException(
                        'Invalid database connection configuration.',
                    );
                }

                $options[$key] = $value;
            }

            $definitions[$name] = new ConnectionDefinition(
                $driver,
                $options,
            );
        }

        $container->instance(
            ConnectionManager::class,
            new ConnectionManager(
                $configuration->string('database.default'),
                $definitions,
                [
                    'mysql' => static fn (
                        ConnectionDefinition $definition,
                    ): Connection => MySqlConnection::fromDefinition(
                        $definition,
                    ),
                ],
            ),
        );
    }

    public function boot(Container $container): void
    {
        // No database network operations during provider boot.
    }
}
