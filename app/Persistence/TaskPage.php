<?php

declare(strict_types=1);

namespace App\Persistence;

/** A page of tasks, with an optional cursor for the next page. */
final readonly class TaskPage
{
    /**
     * @param list<Task> $items
     */
    public function __construct(
        public array $items,
        public ?string $nextCursor,
    ) {
    }
}