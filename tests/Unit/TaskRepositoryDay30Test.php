<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Persistence\Task;
use App\Persistence\TaskRepository;
use Careminate\Database\QueryException;
use Careminate\Database\SqlConnection;
use Careminate\Database\SqlResult;
use Closure;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskRepositoryDay30Test extends TestCase
{
    public function testUpdateBindsAllFieldsAndOwner(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([], 1),
            new SqlResult([self::row('9', '7', 'Changed', 'completed')], 0),
        ];

        $task = new TaskRepository($db)->updateForUser(
            '7',
            '9',
            '  Changed  ',
            'New details',
            'completed',
        );

        self::assertInstanceOf(Task::class, $task);
        self::assertSame('Changed', $task->title);
        self::assertSame('completed', $task->status);
        self::assertStringContainsString(
            'WHERE user_id = :user_id AND id = :id',
            $db->statements[0]['sql'],
        );
        self::assertSame([
            'title' => 'Changed',
            'description' => 'New details',
            'status' => 'completed',
            'user_id' => '7',
            'id' => '9',
        ], $db->statements[0]['parameters']);
    }

    public function testUpdateOfMissingOrForeignTaskReturnsNull(): void
    {
        $db = new Day30SqlSpy();
        $task = new TaskRepository($db)->updateForUser(
            '7',
            '9',
            'Valid',
            null,
            'pending',
        );

        self::assertNull($task);
        self::assertCount(2, $db->statements);
        self::assertSame(
            ['user_id' => '7', 'id' => '9'],
            $db->statements[1]['parameters'],
        );
    }

    public function testDeleteReturnsTrueOnlyWhenRowDeleted(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([], 1),
            new SqlResult([['affected' => '1']], 0),
        ];

        self::assertTrue(
            new TaskRepository($db)->deleteForUser('7', '9'),
        );
        self::assertStringContainsString(
            'WHERE user_id = :user_id AND id = :id',
            $db->statements[0]['sql'],
        );
        self::assertSame(
            ['user_id' => '7', 'id' => '9'],
            $db->statements[0]['parameters'],
        );
        self::assertSame(
            'SELECT ROW_COUNT() AS affected',
            $db->statements[1]['sql'],
        );
    }

    public function testDeleteReturnsFalseForUnownedOrMissingTask(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([], 0),
            new SqlResult([['affected' => 0]], 0),
        ];

        self::assertFalse(
            new TaskRepository($db)->deleteForUser('7', '9'),
        );
    }

    public function testDeleteRejectsUnexpectedRowCount(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([], 0),
            new SqlResult([['affected' => 2]], 0),
        ];

        $this->expectException(QueryException::class);

        new TaskRepository($db)->deleteForUser('7', '9');
    }

    public function testPaginationReturnsCursorAndExcludesExtraRow(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([
                self::row('9', '7'),
                self::row('8', '7'),
                self::row('7', '7'),
            ], 0),
        ];

        $page = new TaskRepository($db)->paginateForUser('7', 2);

        self::assertSame(
            ['9', '8'],
            array_map(
                static fn (Task $task): string => $task->id,
                $page->items,
            ),
        );
        self::assertSame('8', $page->nextCursor);
        self::assertStringContainsString(
            'ORDER BY id DESC LIMIT 3',
            $db->statements[0]['sql'],
        );
        self::assertSame(
            ['user_id' => '7'],
            $db->statements[0]['parameters'],
        );
    }

    public function testPaginationAppliesStatusAndCursorAsBindings(): void
    {
        $db = new Day30SqlSpy();
        $db->results = [
            new SqlResult([
                self::row('5', '7', 'Task', 'completed'),
            ], 0),
        ];

        $page = new TaskRepository($db)->paginateForUser(
            '7',
            2,
            'completed',
            '8',
        );

        self::assertCount(1, $page->items);
        self::assertNull($page->nextCursor);
        self::assertStringContainsString(
            'AND status = :status',
            $db->statements[0]['sql'],
        );
        self::assertStringContainsString(
            'AND id < :after_id',
            $db->statements[0]['sql'],
        );
        self::assertSame([
            'user_id' => '7',
            'status' => 'completed',
            'after_id' => '8',
        ], $db->statements[0]['parameters']);
    }

    public function testEmptyPageHasNoNextCursor(): void
    {
        $db = new Day30SqlSpy();
        $page = new TaskRepository($db)->paginateForUser('7');

        self::assertSame([], $page->items);
        self::assertNull($page->nextCursor);
        self::assertStringContainsString(
            'LIMIT 21',
            $db->statements[0]['sql'],
        );
    }

    /** @return iterable<string, array{int}> */
    public static function invalidSizes(): iterable
    {
        yield 'zero' => [0];
        yield 'negative' => [-1];
        yield 'over maximum' => [51];
    }

    #[DataProvider('invalidSizes')]
    public function testInvalidPageSizeDoesNotExecuteSql(int $size): void
    {
        $db = new Day30SqlSpy();

        try {
            new TaskRepository($db)->paginateForUser('7', $size);
            self::fail('Page size must fail.');
        } catch (InvalidArgumentException) {
            self::assertSame([], $db->statements);
        }
    }

    /** @return iterable<string, array{string}> */
    public static function invalidStatuses(): iterable
    {
        yield 'empty' => [''];
        yield 'unsupported' => ['archived'];
        yield 'injection' => ["pending' OR 1=1 --"];
    }

    #[DataProvider('invalidStatuses')]
    public function testInvalidFilterDoesNotExecuteSql(
        string $status,
    ): void {
        $db = new Day30SqlSpy();

        try {
            new TaskRepository($db)->paginateForUser(
                '7',
                20,
                $status,
            );
            self::fail('Status filter must fail.');
        } catch (InvalidArgumentException) {
            self::assertSame([], $db->statements);
        }
    }

    public function testInvalidCursorDoesNotExecuteSql(): void
    {
        $db = new Day30SqlSpy();

        try {
            new TaskRepository($db)->paginateForUser(
                '7',
                20,
                null,
                '0',
            );
            self::fail('Cursor must fail.');
        } catch (InvalidArgumentException) {
            self::assertSame([], $db->statements);
        }
    }

    public function testInvalidUpdateDoesNotExecuteSql(): void
    {
        $db = new Day30SqlSpy();

        try {
            new TaskRepository($db)->updateForUser(
                '7',
                '9',
                'Valid',
                null,
                'deleted',
            );
            self::fail('Status must fail.');
        } catch (InvalidArgumentException) {
            self::assertSame([], $db->statements);
        }
    }

    /** @return array<string, mixed> */
    private static function row(
        string $id,
        string $userId,
        string $title = 'Task',
        string $status = 'pending',
    ): array {
        return [
            'id' => $id,
            'user_id' => $userId,
            'title' => $title,
            'description' => null,
            'status' => $status,
            'created_at' => '2026-11-07 10:20:30.000000',
            'updated_at' => '2026-11-07 10:20:30.000000',
        ];
    }
}

final class Day30SqlSpy implements SqlConnection
{
    /** @var list<SqlResult> */
    public array $results = [];

    /** @var list<array{
     *     sql: string,
     *     parameters: array<int|string, mixed>
     * }> */
    public array $statements = [];

    public function driver(): string
    {
        return 'mysql';
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

        return array_shift($this->results) ?? new SqlResult([], 0);
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
        return '9';
    }
}
