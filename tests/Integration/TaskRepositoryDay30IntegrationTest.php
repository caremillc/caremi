<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Persistence\Task;
use App\Persistence\TaskRepository;
use Careminate\Database\ConnectionDefinition;
use Careminate\Database\MySql\MySqlConnection;
use PHPUnit\Framework\TestCase;

/** Requires the migrated, disposable careminate_schema_test MySQL schema. */
final class TaskRepositoryDay30IntegrationTest extends TestCase
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
        $user = getenv('CAREMINATE_TEST_MYSQL_USERNAME');
        $password = getenv('CAREMINATE_TEST_MYSQL_PASSWORD');
        $port = getenv('CAREMINATE_TEST_MYSQL_PORT');

        if (
            ! is_string($host) || $host === ''
            || ! is_string($user) || $user === ''
            || ! is_string($password) || $password === ''
        ) {
            self::markTestSkipped(
                'MySQL test credentials are required.',
            );
        }

        $this->database = MySqlConnection::fromDefinition(
            new ConnectionDefinition('mysql', [
                'host' => $host,
                'port' => is_string($port) && ctype_digit($port)
                    ? (int) $port
                    : 3306,
                'database' => 'careminate_schema_test',
                'username' => $user,
                'password' => $password,
                'connect_timeout' => 3,
            ]),
        );

        $this->database->connect();
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

    public function testUpdateOwnedTaskAndPreserveIdentifier(): void
    {
        $original = $this->tasks->create(
            $this->userA,
            'Original',
        );

        $updated = $this->tasks->updateForUser(
            $this->userA,
            $original->id,
            '  Revised  ',
            'New details',
            'in_progress',
        );

        self::assertInstanceOf(Task::class, $updated);
        self::assertSame($original->id, $updated->id);
        self::assertSame('Revised', $updated->title);
        self::assertSame('New details', $updated->description);
        self::assertSame('in_progress', $updated->status);
        self::assertSame($original->createdAt, $updated->createdAt);
    }

    public function testUpdateWithUnchangedValuesStillReturnsTask(): void
    {
        $original = $this->tasks->create(
            $this->userA,
            'Unchanged',
        );

        $updated = $this->tasks->updateForUser(
            $this->userA,
            $original->id,
            'Unchanged',
            null,
            'pending',
        );

        self::assertSame($original->id, $updated?->id);
    }

    public function testCannotUpdateAnotherUsersTask(): void
    {
        $original = $this->tasks->create(
            $this->userB,
            'Private',
        );

        self::assertNull(
            $this->tasks->updateForUser(
                $this->userA,
                $original->id,
                'Intrusion',
                null,
                'completed',
            ),
        );

        $current = $this->tasks->findForUser(
            $this->userB,
            $original->id,
        );

        self::assertInstanceOf(Task::class, $current);

        self::assertSame('Private', $current->title);
        self::assertSame('pending', $current->status);
    }

    public function testDeleteOwnedTaskAndReportNotFoundOnRetry(): void
    {
        $original = $this->tasks->create(
            $this->userA,
            'Delete me',
        );

        self::assertTrue(
            $this->tasks->deleteForUser(
                $this->userA,
                $original->id,
            ),
        );

        self::assertNull(
            $this->tasks->findForUser(
                $this->userA,
                $original->id,
            ),
        );

        self::assertFalse(
            $this->tasks->deleteForUser(
                $this->userA,
                $original->id,
            ),
        );
    }

    public function testCannotDeleteAnotherUsersTask(): void
    {
        $original = $this->tasks->create(
            $this->userB,
            'Keep me',
        );

        self::assertFalse(
            $this->tasks->deleteForUser(
                $this->userA,
                $original->id,
            ),
        );

        self::assertNotNull(
            $this->tasks->findForUser(
                $this->userB,
                $original->id,
            ),
        );
    }

    public function testCursorPaginationTraversesOwnedTasksWithoutDuplicates(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $this->tasks->create(
                $this->userA,
                'A ' . $i,
            );
        }

        $this->tasks->create(
            $this->userB,
            'Foreign',
        );

        $first = $this->tasks->paginateForUser(
            $this->userA,
            2,
        );

        self::assertCount(2, $first->items);
        self::assertNotNull($first->nextCursor);

        $second = $this->tasks->paginateForUser(
            $this->userA,
            2,
            null,
            $first->nextCursor,
        );

        self::assertCount(2, $second->items);
        self::assertNotNull($second->nextCursor);

        $third = $this->tasks->paginateForUser(
            $this->userA,
            2,
            null,
            $second->nextCursor,
        );

        self::assertCount(1, $third->items);
        self::assertNull($third->nextCursor);

        $ids = array_map(
            static fn (Task $task): string => $task->id,
            [...$first->items, ...$second->items, ...$third->items],
        );

        self::assertCount(5, array_unique($ids));
    }

    public function testStatusFilterPersistsAcrossPages(): void
    {
        for ($i = 0; $i < 3; ++$i) {
            $created = $this->tasks->create(
                $this->userA,
                'Completed ' . $i,
            );

            $this->tasks->updateForUser(
                $this->userA,
                $created->id,
                'Completed ' . $i,
                null,
                'completed',
            );
        }

        $this->tasks->create($this->userA, 'Pending');

        $other = $this->tasks->create(
            $this->userB,
            'Foreign completed',
        );

        $this->tasks->updateForUser(
            $this->userB,
            $other->id,
            'Foreign completed',
            null,
            'completed',
        );

        $first = $this->tasks->paginateForUser(
            $this->userA,
            2,
            'completed',
        );

        $second = $this->tasks->paginateForUser(
            $this->userA,
            2,
            'completed',
            $first->nextCursor,
        );

        self::assertCount(2, $first->items);
        self::assertCount(1, $second->items);
        self::assertNull($second->nextCursor);

        foreach ([...$first->items, ...$second->items] as $task) {
            self::assertSame($this->userA, $task->userId);
            self::assertSame('completed', $task->status);
        }
    }

    public function testMaximumPageSizeBoundary(): void
    {
        for ($i = 0; $i < 51; ++$i) {
            $this->tasks->create(
                $this->userA,
                'Boundary ' . $i,
            );
        }

        $first = $this->tasks->paginateForUser(
            $this->userA,
            50,
        );

        self::assertCount(50, $first->items);
        self::assertNotNull($first->nextCursor);

        $second = $this->tasks->paginateForUser(
            $this->userA,
            50,
            null,
            $first->nextCursor,
        );

        self::assertCount(1, $second->items);
        self::assertNull($second->nextCursor);
    }

    private function createUser(): string
    {
        $this->database->execute(
            'INSERT INTO users (email, display_name, password_hash) '
                . 'VALUES (:email, :name, :hash)',
            [
                'email' => 'day30-'
                    . bin2hex(random_bytes(8))
                    . '@example.test',
                'name' => 'Day 30 Test User',
                'hash' => password_hash(
                    'test-only-password',
                    PASSWORD_DEFAULT,
                ),
            ],
        );

        return $this->database->lastInsertId();
    }
}
