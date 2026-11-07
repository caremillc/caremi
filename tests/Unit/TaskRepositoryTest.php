<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Persistence\Task;
use App\Persistence\TaskRepository;
use Careminate\Database\SqlConnection;
use Careminate\Database\SqlResult;
use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class TaskRepositoryTest extends TestCase
{
    public function testCreateUsesBindingsAndReadsInsertedTask(): void
    {
        $database = new RecordingSqlConnection();
        $database->results = [
            new SqlResult([], 1),
            new SqlResult([self::row()], 0),
        ];

        $task = new TaskRepository($database)
            ->create('7', '  First task  ');

        self::assertSame('42', $task->id);
        self::assertSame('7', $task->userId);
        self::assertSame('First task', $task->title);
        self::assertSame('pending', $task->status);
        self::assertCount(2, $database->statements);

        self::assertSame(
            [
                'user_id' => '7',
                'title' => 'First task',
                'description' => null,
            ],
            $database->statements[0]['parameters'],
        );

        self::assertSame(
            ['user_id' => '7', 'id' => '42'],
            $database->statements[1]['parameters'],
        );

        self::assertStringContainsString(
            'WHERE user_id = :user_id AND id = :id',
            $database->statements[1]['sql'],
        );
    }

    public function testUntrustedTextIsBoundRatherThanInterpolated(): void
    {
        $database = new RecordingSqlConnection();
        $database->results = [
            new SqlResult([], 1),
            new SqlResult([self::row()], 0),
        ];

        $untrusted = "' OR 1=1 --";

        new TaskRepository($database)
            ->create('7', $untrusted);

        self::assertStringNotContainsString(
            $untrusted,
            $database->statements[0]['sql'],
        );

        self::assertSame(
            $untrusted,
            $database->statements[0]['parameters']['title'],
        );
    }

    public function testFindReturnsNullForMissingTask(): void
    {
        $database = new RecordingSqlConnection();

        $task = new TaskRepository($database)
            ->findForUser('7', '999');

        self::assertNull($task);

        self::assertSame(
            ['user_id' => '7', 'id' => '999'],
            $database->statements[0]['parameters'],
        );
    }

    public function testListIsOwnerScopedAndBounded(): void
    {
        $database = new RecordingSqlConnection();
        $database->results = [
            new SqlResult([self::row()], 0),
        ];

        $tasks = new TaskRepository($database)
            ->listForUser('7');

        self::assertCount(1, $tasks);
        self::assertInstanceOf(Task::class, $tasks[0]);

        self::assertSame(
            ['user_id' => '7'],
            $database->statements[0]['parameters'],
        );

        self::assertStringContainsString(
            'WHERE user_id = :user_id',
            $database->statements[0]['sql'],
        );

        self::assertStringContainsString(
            'ORDER BY id DESC LIMIT 50',
            $database->statements[0]['sql'],
        );
    }

    /** @return iterable<string, array{string}> */
    public static function invalidIds(): iterable
    {
        yield 'empty' => [''];
        yield 'zero' => ['0'];
        yield 'negative' => ['-1'];
        yield 'non-numeric' => ['1 OR 1=1'];
        yield 'leading zero' => ['01'];
        yield 'overflow' => ['18446744073709551616'];
    }

    #[DataProvider('invalidIds')]
    public function testInvalidIdentifiersFailBeforeSql(string $id): void
    {
        $database = new RecordingSqlConnection();

        try {
            new TaskRepository($database)
                ->findForUser($id, '1');

            self::fail('Invalid identifier must fail.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame(
                'Invalid database identifier.',
                $exception->getMessage(),
            );

            self::assertSame([], $database->statements);
        }
    }

    /** @return iterable<string, array{string}> */
    public static function invalidTitles(): iterable
    {
        yield 'empty' => [''];
        yield 'spaces only' => ['   '];
        yield 'too long' => [str_repeat('a', 201)];
        yield 'invalid utf8' => ["\xC3\x28"];
        yield 'NUL' => ["bad\0text"];
    }

    #[DataProvider('invalidTitles')]
    public function testInvalidTitlesFailBeforeSql(string $title): void
    {
        $database = new RecordingSqlConnection();

        try {
            new TaskRepository($database)
                ->create('7', $title);

            self::fail('Invalid title must fail.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame(
                'Invalid task title.',
                $exception->getMessage(),
            );

            self::assertSame([], $database->statements);
        }
    }

    public function testOversizedDescriptionFailsBeforeSql(): void
    {
        $database = new RecordingSqlConnection();

        $this->expectException(
            InvalidArgumentException::class,
        );

        new TaskRepository($database)->create(
            '7',
            'Valid',
            str_repeat('x', 65536),
        );
    }

    public function testMalformedDatabaseRowsAreRejected(): void
    {
        $database = new RecordingSqlConnection();
        $database->results = [
            new SqlResult([['id' => 42]], 0),
        ];

        $this->expectException(
            UnexpectedValueException::class,
        );

        new TaskRepository($database)
            ->findForUser('7', '42');
    }

    public function testOtherDriverIsNotAccidentallyDeclaredSupported(): void
    {
        $database = new RecordingSqlConnection();
        $database->driverName = 'sqlite';

        $this->expectException(
            InvalidArgumentException::class,
        );

        new TaskRepository($database);
    }

    /** @return array<string, mixed> */
    private static function row(): array
    {
        return [
            'id' => 42,
            'user_id' => '7',
            'title' => 'First task',
            'description' => null,
            'status' => 'pending',
            'created_at' => '2026-11-07 10:20:30.000000',
            'updated_at' => '2026-11-07 10:20:30.000000',
        ];
    }
}

/** Driver-neutral spy; no PDO or MySQL connection required. */
final class RecordingSqlConnection implements SqlConnection
{
    public string $driverName = 'mysql';
    public string $insertId = '42';

    /** @var list<SqlResult> */
    public array $results = [];

    /** @var list<array{
     *     sql: string,
     *     parameters: array<int|string, mixed>
     * }> */
    public array $statements = [];

    public function driver(): string
    {
        return $this->driverName;
    }

    public function connect(): void
    {
    }

    public function isConnected(): bool
    {
        return false;
    }

    public function disconnect(): void
    {
    }

    /** @param array<int|string, mixed> $parameters */
    public function execute(
        string $sql,
        array $parameters = [],
    ): SqlResult {
        $this->statements[] = [
            'sql' => $sql,
            'parameters' => $parameters,
        ];

        return array_shift($this->results)
            ?? new SqlResult([], 0);
    }

    public function beginTransaction(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollBack(): void
    {
    }

    public function inTransaction(): bool
    {
        return false;
    }

    /**
     * @template T
     * @param Closure(SqlConnection): T $operation
     * @return T
     */
    public function transactional(Closure $operation): mixed
    {
        return $operation($this);
    }

    public function lastInsertId(): string
    {
        return $this->insertId;
    }
}
