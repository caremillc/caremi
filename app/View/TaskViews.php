<?php

declare(strict_types=1);

namespace App\View;

use App\Persistence\Task;
use App\Persistence\TaskPage;
use Careminate\Http\Response;
use InvalidArgumentException;
use Twig\Environment;

final readonly class TaskViews
{
    public function __construct(
        private Environment $twig,
    ) {
    }

    public function list(
        TaskPage $page,
        ?string $status = null,
    ): Response {
        if (
            $status !== null
            && ! in_array(
                $status,
                ['pending', 'in_progress', 'completed'],
                true,
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid task status filter.',
            );
        }

        $nextUrl = null;

        if ($page->nextCursor !== null) {
            $query = ['after' => $page->nextCursor];

            if ($status !== null) {
                $query = [
                    'status' => $status,
                    'after' => $page->nextCursor,
                ];
            }

            $nextUrl = '/tasks?' . http_build_query(
                $query,
                '',
                '&',
                PHP_QUERY_RFC3986,
            );
        }

        return $this->html(
            $this->twig->render(
                'tasks/index.html.twig',
                [
                    'tasks' => $page->items,
                    'status' => $status,
                    'nextUrl' => $nextUrl,
                ],
            ),
        );
    }

    public function detail(Task $task): Response
    {
        return $this->html(
            $this->twig->render(
                'tasks/show.html.twig',
                ['task' => $task],
            ),
        );
    }

    public function createForm(
        TaskFormData $form,
    ): Response {
        return $this->formHtml(
            $this->twig->render(
                'tasks/create.html.twig',
                ['form' => $form],
            ),
            $form,
        );
    }

    public function editForm(
        Task $task,
        TaskFormData $form,
    ): Response {
        return $this->formHtml(
            $this->twig->render(
                'tasks/edit.html.twig',
                [
                    'task' => $task,
                    'form' => $form,
                ],
            ),
            $form,
        );
    }

    private function formHtml(
        string $body,
        TaskFormData $form,
    ): Response {
        return $this->html(
            $body,
            $form->isValid() ? 200 : 422,
            true,
        );
    }

    private function html(
        string $body,
        int $status = 200,
        bool $noStore = false,
    ): Response {
        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($noStore) {
            $headers['Cache-Control'] = 'no-store';
        }

        return new Response(
            $status,
            $headers,
            $body,
        );
    }
}
