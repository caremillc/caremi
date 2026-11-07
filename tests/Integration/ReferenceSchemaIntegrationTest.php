<?php

declare(strict_types=1);

namespace Tests\Integration;

use Careminate\Database\ConnectionDefinition;
use Careminate\Database\Migrations\MySqlMigrationRunner;
use Careminate\Database\MySql\MySqlConnection;
use Careminate\Database\QueryException;
use PHPUnit\Framework\TestCase;

/**
 * Dedicated integration database: careminate_schema_test.
 *
 * Schema changes persist; individual test records roll back.
 */
final class ReferenceSchemaIntegrationTest extends TestCase
{
    private MySqlConnection $database;

    private MySqlMigrationRunner $runner;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_mysql')) {
            self::markTestSkipped('pdo_mysql is required.');
        }

        if (
            getenv('CAREMINATE_TEST_MYSQL_SCHEMA_DATABASE')
                !== 'careminate_schema_test'
        ) {
            self::markTestSkipped(
                'Use the dedicated careminate_schema_test database.',
            );
        }

        $host = getenv('CAREMINATE_TEST_MYSQL_HOST');
        $port = getenv('CAREMINATE_TEST_MYSQL_PORT');
        $username = getenv('CAREMINATE_TEST_MYSQL_USERNAME');
        $password = getenv('CAREMINATE_TEST_MYSQL_PASSWORD');

        if (
            ! is_string($host) || $host === ''
            || ! is_string($username) || $username === ''
            || ! is_string($password) || $password === ''
        ) {
            self::markTestSkipped(
                'MySQL integration credentials are required.',
            );
        }

        $definition = new ConnectionDefinition(
            'mysql',
            [
                'host' => $host,
                'port' => is_string($port) && ctype_digit($port)
                    ? (int) $port
                    : 3306,
                'database' => 'careminate_schema_test',
                'username' => $username,
                'password' => $password,
                'connect_timeout' => 3,
            ],
        );

        $this->database = MySqlConnection::fromDefinition(
            $definition,
        );

        $this->database->connect();

        $this->runner = new MySqlMigrationRunner(
            $this->database,
            dirname(__DIR__, 2) . '/database/migrations',
        );

        // Safe rerun; refuses incomplete migration histories.
        $this->runner->migrate();

        $this->database->beginTransaction();
    }

    protected function tearDown(): void
    {
        if (! isset($this->database)) {
            return;
        }

        try {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
        } finally {
            $this->database->disconnect();
        }
    }

    public function testFreshSchemaHasTwoAppliedMigrationsAndRerunsCleanly(): void
    {
        $rows = $this->runner->status();

        self::assertSame(
            [
                '20261104_000001_create_users',
                '20261104_000002_create_tasks',
            ],
            array_column($rows, 'name'),
        );

        self::assertSame(
            ['applied', 'applied'],
            array_column($rows, 'status'),
        );

        // Migrations must execute outside application transactions.
        $this->database->rollBack();

        self::assertSame(
            0,
            $this->runner->migrate(),
        );

        $this->database->beginTransaction();
    }

    public function testUserEmailMustBeUnique(): void
    {
        $email = $this->uniqueEmail();

        $this->createUser($email);

        $this->expectException(QueryException::class);

        $this->createUser($email);
    }

    public function testUserEmailMustBeNormalizedToLowercase(): void
    {
        $this->expectException(QueryException::class);

        $this->createUser(
            'UpperCase-'
                . bin2hex(random_bytes(6))
                . '@example.test',
        );
    }

    public function testTasksRequireAnExistingOwner(): void
    {
        $this->expectException(QueryException::class);

        $this->database->execute(
            'INSERT INTO tasks (user_id, title) '
                . 'VALUES (:owner, :title)',
            [
                'owner' => '18446744073709551615',
                'title' => 'Orphan',
            ],
        );
    }

    public function testInvalidTaskStatusIsRejected(): void
    {
        $owner = $this->createUser($this->uniqueEmail());

        $this->expectException(QueryException::class);

        $this->createTask(
            $owner,
            'Invalid status',
            'not_a_status',
        );
    }

    public function testBlankTaskTitleIsRejected(): void
    {
        $owner = $this->createUser($this->uniqueEmail());

        $this->expectException(QueryException::class);

        $this->createTask($owner, '   ');
    }

    public function testOwnerAndStatusIndexesExist(): void
    {
        $rows = $this->database->execute(
            'SELECT DISTINCT INDEX_NAME AS index_name '
                . 'FROM information_schema.STATISTICS '
                . 'WHERE TABLE_SCHEMA = DATABASE() '
                . 'AND TABLE_NAME = :name',
            ['name' => 'tasks'],
        )->rows();

       $names = array_map(
    static function (array $row): string {
        $name = $row['index_name'] ?? null;

        if (! is_string($name)) {
            throw new \UnexpectedValueException(
                'Invalid database index name.',
            );
        }

        return $name;
    },
    $rows,
);

        self::assertContains(
            'idx_tasks_user_id_id',
            $names,
        );

        self::assertContains(
            'idx_tasks_user_status_id',
            $names,
        );
    }

    public function testOwnerAndStatusQueriesStayScoped(): void
    {
        $ownerA = $this->createUser($this->uniqueEmail());
        $ownerB = $this->createUser($this->uniqueEmail());

        $this->createTask($ownerA, 'A pending');
        $this->createTask($ownerA, 'A done', 'completed');
        $this->createTask($ownerB, 'B pending');

        $result = $this->database->execute(
            'SELECT title FROM tasks '
                . 'WHERE user_id = :owner '
                . 'AND status = :status '
                . 'ORDER BY id DESC LIMIT 20',
            [
                'owner' => $ownerA,
                'status' => 'pending',
            ],
        );

        self::assertSame(
            [['title' => 'A pending']],
            $result->rows(),
        );
    }

    public function testDeletingUserCascadesToOwnedTasks(): void
    {
        $owner = $this->createUser($this->uniqueEmail());

        $this->createTask($owner, 'Owned task');

        self::assertSame(1, $this->taskCount($owner));

        $this->database->execute(
            'DELETE FROM users WHERE id = :id',
            ['id' => $owner],
        );

        self::assertSame(0, $this->taskCount($owner));
    }

    public function testConnectionUsesUtcTimeZone(): void
    {
        $value = $this->database->execute(
            'SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP()) AS utc_offset',
        )->first()['utc_offset'] ?? null;

        self::assertSame('00:00:00', $value);
    }

    public function testOptionalDescriptionAndStatusDefaultAndTimestamps(): void
    {
        $owner = $this->createUser($this->uniqueEmail());
        $id = $this->createTask(
            $owner,
            'Without description',
        );

        $row = $this->database->execute(
            'SELECT description, status, created_at, updated_at '
                . 'FROM tasks WHERE id = :id AND user_id = :owner',
            [
                'id' => $id,
                'owner' => $owner,
            ],
        )->first();

        self::assertNotNull($row);
        self::assertNull($row['description']);
        self::assertSame('pending', $row['status']);
        self::assertIsString($row['created_at']);
        self::assertIsString($row['updated_at']);
    }

    private function uniqueEmail(): string
    {
        return 'case-'
            . bin2hex(random_bytes(8))
            . '@example.test';
    }

    private function createUser(string $email): string
    {
        $this->database->execute(
            'INSERT INTO users '
                . '(email, display_name, password_hash) '
                . 'VALUES (:email, :name, :hash)',
            [
                'email' => $email,
                'name' => 'Test User',
                'hash' => password_hash(
                    'temporary-test-password',
                    PASSWORD_DEFAULT,
                ),
            ],
        );

        return $this->database->lastInsertId();
    }

    private function createTask(
        string $owner,
        string $title,
        ?string $status = null,
    ): string {
        if ($status === null) {
            $this->database->execute(
                'INSERT INTO tasks (user_id, title) '
                    . 'VALUES (:owner, :title)',
                [
                    'owner' => $owner,
                    'title' => $title,
                ],
            );
        } else {
            $this->database->execute(
                'INSERT INTO tasks (user_id, title, status) '
                    . 'VALUES (:owner, :title, :status)',
                [
                    'owner' => $owner,
                    'title' => $title,
                    'status' => $status,
                ],
            );
        }

        return $this->database->lastInsertId();
    }

    private function taskCount(string $owner): int
    {
        $total = $this->database->execute(
            'SELECT COUNT(*) AS total FROM tasks '
                . 'WHERE user_id = :owner',
            ['owner' => $owner],
        )->first()['total'] ?? null;

        self::assertTrue(
            is_int($total) || is_string($total),
        );

        return (int) $total;
    }
}
