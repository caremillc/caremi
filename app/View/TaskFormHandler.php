<?php

declare(strict_types=1);

namespace App\View;

use App\Persistence\TaskRepository;
use Careminate\Http\Response;

/**
 * Application form workflow, not an HTTP route or authentication boundary.
 * Caller must supply a trusted authenticated user ID after CSRF verification.
 */
final readonly class TaskFormHandler
{
    public function __construct(
        private TaskRepository $tasks,
        private TaskViews $views,
        private TaskFormValidator $validator,
    ) {
    }

    public function newForm(): Response
    {
        return $this->views->createForm(
            TaskFormData::blank(),
        );
    }

    public function editForUser(
        string $userId,
        string $taskId,
    ): Response {
        $task = $this->tasks->findForUser(
            $userId,
            $taskId,
        );

        return $task === null
            ? self::notFound()
            : $this->views->editForm(
                $task,
                TaskFormData::fromTask($task),
            );
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function createForUser(
        string $userId,
        array $fields,
    ): Response {
        $form = $this->validator->validateForCreate(
            $fields,
        );

        if (! $form->isValid()) {
            return $this->views->createForm($form);
        }

        $task = $this->tasks->create(
            $userId,
            trim($form->title),
            $form->descriptionForStorage(),
        );

        return self::redirectToTask($task->id);
    }

    /**
     * @param array<string, mixed> $fields
     */
    public function updateForUser(
        string $userId,
        string $taskId,
        array $fields,
    ): Response {
        $task = $this->tasks->findForUser(
            $userId,
            $taskId,
        );

        if ($task === null) {
            return self::notFound();
        }

        $form = $this->validator->validate(
            $fields,
        );

        if (! $form->isValid()) {
            return $this->views->editForm(
                $task,
                $form,
            );
        }

        $updated = $this->tasks->updateForUser(
            $userId,
            $taskId,
            trim($form->title),
            $form->descriptionForStorage(),
            $form->status,
        );

        return $updated === null
            ? self::notFound()
            : self::redirectToTask($updated->id);
    }

    private static function redirectToTask(
        string $id,
    ): Response {
        return new Response(
            303,
            [
                'Location' => '/tasks/' . rawurlencode($id),
                'Cache-Control' => 'no-store',
            ],
        );
    }

    private static function notFound(): Response
    {
        return new Response(
            404,
            [
                'Content-Type' => 'text/plain; charset=UTF-8',
                'Cache-Control' => 'no-store',
            ],
            'Not found.',
        );
    }
}
