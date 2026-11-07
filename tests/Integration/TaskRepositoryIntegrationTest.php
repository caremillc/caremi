<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Persistence\Task;
use App\Persistence\TaskRepository;
use Careminate\Database\ConnectionDefinition;
use Careminate\Database\MySql\MySqlConnection;
use Careminate\Database\QueryException;
use PHPUnit\Framework\TestCase;

/** Run only against the dedicated, migrated careminate_schema_test database. */
final class TaskRepositoryIntegrationTest extends TestCase
{
    private MySqlConnection $database;
    private TaskRepository $tasks;
    private string $userA;
    private string $userB;

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
                'Only careminate_schema_test is allowed.',
            );
        }

        $host = getenv('CAREMINATE_TEST_MYSQL_HOST');
        $username = getenv('CAREMINATE_TEST_MYSQL_USERNAME');
        $password = getenv('CAREMINATE_TEST_MYSQL_PASSWORD');
        $port = getenv('CAREMINATE_TEST_MYSQL_PORT');

        if (
            ! is_string($host) || $host === ''
            || ! is_string($username) || $username === ''
            || ! is_string($password) || $password === ''
        ) {
            self::markTestSkipped(
                'MySQL integration test credentials are required.',
            );
        }

        $this->database = MySqlConnection::fromDefinition(
            new ConnectionDefinition('mysql', [
                'host' => $host,
                'port' => is_string($port) && ctype_digit($port)
                    ? (int) $port
                    : 3306,
                'database' => 'careminate_schema_test',
                'username' => $username,
                'password' => $password,
                'connect_timeout' => 3,
            ]),
        );

        $this->database->connect();

        // Day 28 schema must already be installed.
        $this->database->execute('SELECT id FROM users LIMIT 0');
        $this->database->execute('SELECT id FROM tasks LIMIT 0');

        $this->database->beginTransaction();

        $this->tasks = new TaskRepository($this->database);
        $this->userA = $this->createUser();
        $this->userB = $this->createUser();
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

    public function testCreatePersistsTaskWithDefaultStatusAndTimestamps(): void
    {
        $task = $this->tasks->create(
            $this->userA,
            '  First task  ',
        );

        self::assertSame($this->userA, $task->userId);
        self::assertSame('First task', $task->title);
        self::assertNull($task->description);
        self::assertSame('pending', $task->status);
        self::assertNotSame('', $task->createdAt);
        self::assertNotSame('', $task->updatedAt);

        self::assertSame(
            $task->id,
            $this->tasks->findForUser(
                $this->userA,
                $task->id,
            )?->id,
        );
    }

    public function testAnotherUserCannotReadTask(): void
    {
        $task = $this->tasks->create(
            $this->userA,
            'Private task',
        );

        self::assertNull(
            $this->tasks->findForUser(
                $this->userB,
                $task->id,
            ),
        );

        self::assertNotNull(
            $this->tasks->findForUser(
                $this->userA,
                $task->id,
            ),
        );
    }

    public function testUnknownIdReturnsNull(): void
    {
        self::assertNull(
            $this->tasks->findForUser(
                $this->userA,
                '18446744073709551615',
            ),
        );
    }

    public function testListContainsOnlyOwnerTasksNewestFirst(): void
    {
        $first = $this->tasks->create(
            $this->userA,
            'A first',
        );

        $second = $this->tasks->create(
            $this->userA,
            'A second',
        );

        $this->tasks->create(
            $this->userB,
            'B private',
        );

        $rows = $this->tasks->listForUser(
            $this->userA,
        );

        self::assertCount(2, $rows);

        self::assertSame(
            [$second->id, $first->id],
            array_map(
                static fn (Task $task): string => $task->id,
                $rows,
            ),
        );
    }

    public function testDescriptionAndSqlLikeTextRoundTrip(): void
    {
        $title = "' OR 1=1 --";
        $description = 'A multilingual description: مرحبا';

        $task = $this->tasks->create(
            $this->userA,
            $title,
            $description,
        );

        self::assertSame($title, $task->title);
        self::assertSame($description, $task->description);

        self::assertSame(
            $title,
            $this->tasks->findForUser(
                $this->userA,
                $task->id,
            )?->title,
        );
    }

    public function testListIsBoundedToFiftyRecords(): void
    {
        for ($i = 0; $i < 51; ++$i) {
            $this->tasks->create(
                $this->userA,
                'Task ' . $i,
            );
        }

        self::assertCount(
            50,
            $this->tasks->listForUser($this->userA),
        );
    }

    public function testDatabaseRejectsUnknownOwner(): void
    {
        $this->expectException(QueryException::class);

        $this->tasks->create(
            '18446744073709551615',
            'Orphaned task',
        );
    }

    private function createUser(): string
    {
        $this->database->execute(
            'INSERT INTO users (email, display_name, password_hash) '
                . 'VALUES (:email, :name, :hash)',
            [
                'email' => 'day29-'
                    . bin2hex(random_bytes(8))
                    . '@example.test',
                'name' => 'Day 29 Test User',
                'hash' => password_hash(
                    'test-only-password',
                    PASSWORD_DEFAULT,
                ),
            ],
        );

        return $this->database->lastInsertId();
    }
}
