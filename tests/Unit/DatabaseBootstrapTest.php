<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Database\ConnectionManager;
use Careminate\Foundation\Application;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DatabaseBootstrapTest extends TestCase
{
    public function testApplicationBootDoesNotOpenDatabaseConnection(): void
    {
        $app = self::bootApplication();

        try {
            $manager = $app->container()->get(
                ConnectionManager::class,
            );

            self::assertInstanceOf(
                ConnectionManager::class,
                $manager,
            );

            $connection = $manager->connection();

            self::assertSame(
                'mysql',
                $connection->driver(),
            );

            self::assertFalse($connection->isConnected());
        } finally {
            $app->terminate();
        }
    }

    public function testApplicationBootWithBadHostStillNeedsNoDatabase(): void
    {
        $app = self::bootApplication([
            'DB_HOST' => 'invalid;dsn=unsafe',
        ]);

        try {
            self::assertTrue($app->isBooted());

            $manager = $app->container()->get(
                ConnectionManager::class,
            );

            self::assertInstanceOf(
                ConnectionManager::class,
                $manager,
            );

            $this->expectException(
                InvalidArgumentException::class,
            );

            $manager->connection();
        } finally {
            $app->terminate();
        }
    }

    public function testUnsupportedDefaultDriverFailsAtBootstrap(): void
    {
        $this->expectException(
            InvalidArgumentException::class,
        );

        $this->expectExceptionMessage(
            'Default database connection is not configured.',
        );

        self::bootApplication([
            'DB_CONNECTION' => 'sqlite',
        ]);
    }

    /**
     * @param array<string, string> $overrides
     */
    private static function bootApplication(
        array $overrides = [],
    ): Application {
        $environmentOverrides = array_replace(
            [
                'APP_ENV' => 'testing',
                'APP_DEBUG' => 'false',
            ],
            $overrides,
        );

        $externalEnvironment = [];

        $environmentFile = dirname(__DIR__, 2)
            . '/.env.database-test-nonexistent';

        $app = require dirname(__DIR__, 2)
            . '/bootstrap/app.php';

        self::assertInstanceOf(
            Application::class,
            $app,
        );

        return $app;
    }
}
