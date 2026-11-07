<?php

declare(strict_types=1);

namespace App\Persistence;

use Careminate\Database\QueryException;
use Careminate\Database\SqlConnection;
use InvalidArgumentException;

/**
 * MySQL task repository.
 *
 * Every read is scoped by the supplied owner ID.
 * The caller is responsible for establishing trusted identity.
 */
final readonly class TaskRepository
{
    private const string MAX_UNSIGNED_BIGINT =
        '18446744073709551615';

    public function __construct(
        private SqlConnection $database,
    ) {
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

        $title = trim($title);

        if (
            str_contains($title, "\0")
            || preg_match(
                '/\A.{1,200}\z/us',
                $title,
            ) !== 1
        ) {
            throw new InvalidArgumentException(
                'Invalid task title.',
            );
        }

        if (
            $description !== null
            && (
                strlen($description) > 65535
                || str_contains($description, "\0")
                || preg_match('//u', $description) !== 1
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid task description.',
            );
        }

        $this->database->execute(
            'INSERT INTO tasks '
                . '(user_id, title, description) '
                . 'VALUES (:user_id, :title, :description)',
            [
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
            ],
        );

        $id = $this->database->lastInsertId();

        $task = $this->findForUser(
            $userId,
            $id,
        );

        if ($task === null) {
            throw new QueryException(
                'Task creation could not be verified.',
            );
        }

        return $task;
    }

    public function findForUser(
        string $userId,
        string $taskId,
    ): ?Task {
        self::assertIdentifier($userId);
        self::assertIdentifier($taskId);

        $row = $this->database->execute(
            'SELECT '
                . 'id, user_id, title, description, '
                . 'status, created_at, updated_at '
                . 'FROM tasks '
                . 'WHERE user_id = :user_id '
                . 'AND id = :id '
                . 'LIMIT 1',
            [
                'user_id' => $userId,
                'id' => $taskId,
            ],
        )->first();

        if ($row === null) {
            return null;
        }

        return Task::fromDatabaseRow($row);
    }

    /**
     * Day 29: first 50 tasks, newest first.
     *
     * @return list<Task>
     */
    public function listForUser(
        string $userId,
    ): array {
        self::assertIdentifier($userId);

        $rows = $this->database->execute(
            'SELECT '
                . 'id, user_id, title, description, '
                . 'status, created_at, updated_at '
                . 'FROM tasks '
                . 'WHERE user_id = :user_id '
                . 'ORDER BY id DESC '
                . 'LIMIT 50',
            [
                'user_id' => $userId,
            ],
        )->rows();

        return array_map(
            static fn (array $row): Task =>
                Task::fromDatabaseRow($row),
            $rows,
        );
    }

    private static function assertIdentifier(
        string $id,
    ): void {
        $length = strlen($id);

        if (
            preg_match(
                '/\A[1-9][0-9]{0,19}\z/D',
                $id,
            ) !== 1
            || (
                $length === 20
                && strcmp(
                    $id,
                    self::MAX_UNSIGNED_BIGINT,
                ) > 0
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid database identifier.',
            );
        }
    }
}
