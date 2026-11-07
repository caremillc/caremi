<?php

declare(strict_types=1);

namespace App\View;

use App\Persistence\Task;

/** Form display state. Values are untrusted until validated. */
final readonly class TaskFormData
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $status,
        public array $errors = [],
    ) {
    }

    public static function blank(): self
    {
        return new self('', '', 'pending');
    }

    public static function fromTask(Task $task): self
    {
        return new self(
            $task->title,
            $task->description ?? '',
            $task->status,
        );
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function descriptionForStorage(): ?string
    {
        return $this->description === '' ? null : $this->description;
    }
}
