<?php

declare(strict_types=1);

namespace App\Persistence;

use UnexpectedValueException;

/**
 * Immutable task record from the Day 28 database schema.
 */
final readonly class Task
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $title,
        public ?string $description,
        public string $status,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromDatabaseRow(array $row): self
    {
        $id = self::identifier($row['id'] ?? null);
        $userId = self::identifier($row['user_id'] ?? null);
        $description = $row['description'] ?? null;
        $status = self::text($row['status'] ?? null);

        if (
            ! array_key_exists('description', $row)
            || (
                $description !== null
                && ! is_string($description)
            )
            || ! in_array(
                $status,
                ['pending', 'in_progress', 'completed'],
                true,
            )
        ) {
            throw new UnexpectedValueException(
                'Invalid task database row.',
            );
        }

        return new self(
            $id,
            $userId,
            self::text($row['title'] ?? null),
            $description,
            $status,
            self::text($row['created_at'] ?? null),
            self::text($row['updated_at'] ?? null),
        );
    }

    private static function identifier(mixed $value): string
    {
        if (
            ! is_string($value)
            && ! is_int($value)
        ) {
            throw new UnexpectedValueException(
                'Invalid task database row.',
            );
        }

        $id = (string) $value;

        if (
            preg_match(
                '/\A[1-9][0-9]*\z/D',
                $id,
            ) !== 1
        ) {
            throw new UnexpectedValueException(
                'Invalid task database row.',
            );
        }

        return $id;
    }

    private static function text(mixed $value): string
    {
        if (! is_string($value)) {
            throw new UnexpectedValueException(
                'Invalid task database row.',
            );
        }

        return $value;
    }
}
