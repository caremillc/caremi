<?php

declare(strict_types=1);

namespace App\Console;

use Careminate\Database\ConnectionException;
use Careminate\Database\ConnectionManager;
use Careminate\Database\Migrations\MigrationException;
use Careminate\Database\Migrations\MySqlMigrationRunner;
use Careminate\Database\SqlConnection;
use Careminate\Foundation\Application;
use Throwable;

final class MigrationCommand
{
    /**
     * @param list<string> $arguments
     */
    public static function run(
        string $applicationRoot,
        array $arguments,
    ): int {
        $command = array_shift($arguments);

        if (
            ! in_array(
                $command,
                ['migrate', 'migrate:status'],
                true,
            )
        ) {
            return self::usageError();
        }

        $connectionName = null;
        $lockTimeout = 10;
        $seenConnection = false;
        $seenTimeout = false;

        foreach ($arguments as $argument) {
            if (
                str_starts_with($argument, '--connection=')
                && ! $seenConnection
            ) {
                $connectionName = substr(
                    $argument,
                    strlen('--connection='),
                );

                $seenConnection = true;

                continue;
            }

            if (
                $command === 'migrate'
                && str_starts_with($argument, '--lock-timeout=')
                && ! $seenTimeout
            ) {
                $value = substr(
                    $argument,
                    strlen('--lock-timeout='),
                );

                if (
                    $value === ''
                    || strlen($value) > 2
                    || ! ctype_digit($value)
                ) {
                    return self::usageError();
                }

                $lockTimeout = (int) $value;
                $seenTimeout = true;

                continue;
            }

            return self::usageError();
        }

        if (
            (
                $connectionName !== null
                && preg_match(
                    '/\A[a-z][a-z0-9_]*\z/D',
                    $connectionName,
                ) !== 1
            )
            || $lockTimeout > 30
        ) {
            return self::usageError();
        }

        $application = null;
        $exitCode = 0;

        try {
            $application = require $applicationRoot
                . '/bootstrap/app.php';

            if (! $application instanceof Application) {
                throw new MigrationException(
                    'Application bootstrap failed.',
                );
            }

            $manager = $application
                ->container()
                ->get(ConnectionManager::class);

            if (! $manager instanceof ConnectionManager) {
                throw new MigrationException(
                    'Database connection manager unavailable.',
                );
            }

            $database = $manager->connection(
                $connectionName,
            );

            if (! $database instanceof SqlConnection) {
                throw new MigrationException(
                    'Selected connection does not support SQL.',
                );
            }

            $runner = new MySqlMigrationRunner(
                $database,
                $applicationRoot . '/database/migrations',
                lockTimeoutSeconds: $lockTimeout,
            );

            if ($command === 'migrate:status') {
                foreach ($runner->status() as $row) {
                    printf(
                        "%-10s %-6s %s\n",
                        strtoupper($row['status']),
                        $row['batch'] === null
                            ? '-'
                            : (string) $row['batch'],
                        $row['name'],
                    );
                }
            } else {
                printf(
                    "Migrations applied: %d\n",
                    $runner->migrate(),
                );
            }
        } catch (MigrationException $exception) {
            fwrite(
                STDERR,
                $exception->getMessage() . "\n",
            );

            $exitCode = 70;
        } 
        catch (ConnectionException) {
    fwrite(
        STDERR,
        "Database connection failed. Check MySQL service and application database settings.\n",
    );

    $exitCode = 70;
}
        catch (Throwable) {
    fwrite(
        STDERR,
        "Migration command failed. Check trusted application logs.\n",
    );

    $exitCode = 70;
}

 finally {
            if ($application instanceof Application) {
                try {
                    $application->terminate();
                } catch (Throwable) {
                    fwrite(
                        STDERR,
                        "Application cleanup failed.\n",
                    );

                    $exitCode = 70;
                }
            }
        }

        return $exitCode;
    }

    private static function usageError(): int
    {
        fwrite(
            STDERR,
            "Usage: php careminate migrate [--connection=mysql] [--lock-timeout=0..30]\n"
                . "       php careminate migrate:status [--connection=mysql]\n",
        );

        return 64;
    }
}
