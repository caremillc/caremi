<?php

declare(strict_types=1);

namespace App\Persistence;

use Careminate\Database\QueryException;
use Careminate\Database\SqlConnection;
use InvalidArgumentException;

/** MySQL task persistence, scoped to a trusted owner ID. */
final readonly class TaskRepository
{
    private const string MAX_UNSIGNED_BIGINT = '18446744073709551615';

    public function __construct(private SqlConnection $database)
    {
        if ($database->driver() !== 'mysql') {
            throw new InvalidArgumentException(
                'Task repository requires a MySQL connection.',
            );
        }
    }

    public function create(
        string $userId,
        string $title,
        ?string $description = null,
    ): Task {
        self::assertIdentifier($userId);
        $title = self::validatedTitle($title);
        self::assertDescription($description);

        $this->database->execute(
            'INSERT INTO tasks (user_id, title, description) '
                . 'VALUES (:user_id, :title, :description)',
            [
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
            ],
        );

        $id = $this->database->lastInsertId();
        $task = $this->findForUser($userId, $id);

        if ($task === null) {
            throw new QueryException('Task creation could not be verified.');
        }

        return $task;
    }

    public function findForUser(string $userId, string $taskId): ?Task
    {
        self::assertIdentifier($userId);
        self::assertIdentifier($taskId);

        $row = $this->database->execute(
            'SELECT id, user_id, title, description, status, created_at, updated_at '
                . 'FROM tasks WHERE user_id = :user_id AND id = :id LIMIT 1',
            ['user_id' => $userId, 'id' => $taskId],
        )->first();

        return $row === null ? null : Task::fromDatabaseRow($row);
    }

    /**
     * Legacy Day 29 list: first 50 tasks in descending ID order.
     *
     * @return list<Task>
     */
    public function listForUser(string $userId): array
    {
        self::assertIdentifier($userId);

        $rows = $this->database->execute(
            'SELECT id, user_id, title, description, status, created_at, updated_at '
                . 'FROM tasks WHERE user_id = :user_id ORDER BY id DESC LIMIT 50',
            ['user_id' => $userId],
        )->rows();

        return array_map(
            static fn (array $row): Task => Task::fromDatabaseRow($row),
            $rows,
        );
    }

    /**
     * Replace all editable fields; return null when the task
     * is absent or does not belong to the supplied user.
     */
    public function updateForUser(
        string $userId,
        string $taskId,
        string $title,
        ?string $description,
        string $status,
    ): ?Task {
        self::assertIdentifier($userId);
        self::assertIdentifier($taskId);
        $title = self::validatedTitle($title);
        self::assertDescription($description);
        self::assertStatus($status);

        $this->database->execute(
            'UPDATE tasks SET title = :title, description = :description, '
                . 'status = :status, updated_at = UTC_TIMESTAMP(6) '
                . 'WHERE user_id = :user_id AND id = :id',
            [
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'user_id' => $userId,
                'id' => $taskId,
            ],
        );

        return $this->findForUser($userId, $taskId);
    }

    /** True only when a task owned by this user was actually deleted. */
    public function deleteForUser(string $userId, string $taskId): bool
    {
        self::assertIdentifier($userId);
        self::assertIdentifier($taskId);

        $this->database->execute(
            'DELETE FROM tasks WHERE user_id = :user_id AND id = :id',
            ['user_id' => $userId, 'id' => $taskId],
        );

        // MySQL-specific: consume ROW_COUNT immediately on this same session.
        $row = $this->database->execute(
            'SELECT ROW_COUNT() AS affected',
        )->first();

        $affected = $row['affected'] ?? null;

        if ($affected === 1 || $affected === '1') {
            return true;
        }

        if ($affected === 0 || $affected === '0') {
            return false;
        }

        throw new QueryException('Unexpected task deletion result.');
    }

    /**
     * Cursor is the ID of the final task from the previous page.
     * Pass nextCursor to the next request with the same filter and page size.
     */
    public function paginateForUser(
        string $userId,
        int $perPage = 20,
        ?string $status = null,
        ?string $afterId = null,
    ): TaskPage {
        self::assertIdentifier($userId);

        if ($perPage < 1 || $perPage > 50) {
            throw new InvalidArgumentException(
                'Page size must be between 1 and 50.',
            );
        }

        $sql = 'SELECT id, user_id, title, description, status, created_at, updated_at '
            . 'FROM tasks WHERE user_id = :user_id';

        $parameters = ['user_id' => $userId];

        if ($status !== null) {
            self::assertStatus($status);
            $sql .= ' AND status = :status';
            $parameters['status'] = $status;
        }

        if ($afterId !== null) {
            self::assertIdentifier($afterId);
            $sql .= ' AND id < :after_id';
            $parameters['after_id'] = $afterId;
        }

        // The limit is a validated integer, never raw input.
        // Fetch one extra row to determine whether another page exists.
        $sql .= ' ORDER BY id DESC LIMIT ' . ($perPage + 1);

        $rows = $this->database->execute($sql, $parameters)->rows();
        $hasMore = count($rows) > $perPage;

        $tasks = array_map(
            static fn (array $row): Task => Task::fromDatabaseRow($row),
            array_slice($rows, 0, $perPage),
        );

        return new TaskPage(
            $tasks,
            $hasMore ? $tasks[count($tasks) - 1]->id : null,
        );
    }

    private static function validatedTitle(string $title): string
    {
        $title = trim($title);

        if (
            str_contains($title, "\0")
            || preg_match('/\A.{1,200}\z/us', $title) !== 1
        ) {
            throw new InvalidArgumentException('Invalid task title.');
        }

        return $title;
    }

    private static function assertDescription(?string $description): void
    {
        if (
            $description !== null
            && (
                strlen($description) > 65535
                || str_contains($description, "\0")
                || preg_match('//u', $description) !== 1
            )
        ) {
            throw new InvalidArgumentException('Invalid task description.');
        }
    }

    private static function assertStatus(string $status): void
    {
        if (! in_array(
            $status,
            ['pending', 'in_progress', 'completed'],
            true,
        )) {
            throw new InvalidArgumentException('Invalid task status.');
        }
    }

    private static function assertIdentifier(string $id): void
    {
        $length = strlen($id);

        if (
            preg_match('/\A[1-9][0-9]{0,19}\z/D', $id) !== 1
            || (
                $length === 20
                && strcmp($id, self::MAX_UNSIGNED_BIGINT) > 0
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid database identifier.',
            );
        }
    }
}
