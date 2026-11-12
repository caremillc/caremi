<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Persistence\Task;
use App\Persistence\TaskPage;
use App\Providers\ViewServiceProvider;
use App\View\TaskViews;
use Careminate\Container\Container;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TaskViewsTest extends TestCase
{
    public function testListEscapesUntrustedTitleAndDescription(): void
    {
        $task = self::task(
            '7',
            '<script>alert(1)</script>',
            '<b>unsafe</b> & "quoted"',
        );

        $response = self::views()->list(
            new TaskPage([$task], null),
        );

        self::assertStringContainsString(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            $response->body,
        );

        self::assertStringContainsString(
            '&lt;b&gt;unsafe&lt;/b&gt; &amp; &quot;quoted&quot;',
            $response->body,
        );

        self::assertStringNotContainsString(
            '<script>',
            $response->body,
        );

        self::assertStringNotContainsString(
            '<b>unsafe</b>',
            $response->body,
        );
    }

    public function testDetailEscapesUntrustedContent(): void
    {
        $response = self::views()->detail(
            self::task(
                '7',
                '<img src=x onerror=alert(1)>',
                '<svg onload=alert(1)>',
            ),
        );

        self::assertStringContainsString(
            '&lt;img src=x onerror=alert(1)&gt;',
            $response->body,
        );

        self::assertStringContainsString(
            '&lt;svg onload=alert(1)&gt;',
            $response->body,
        );

        self::assertStringNotContainsString(
            '<img src=',
            $response->body,
        );

        self::assertStringNotContainsString(
            '<svg ',
            $response->body,
        );
    }

    public function testTaskLinkUsesUrlEncoding(): void
    {
        $response = self::views()->list(
            new TaskPage([
                self::task('7/unsafe?x=1', 'Example', null),
            ], null),
        );

        self::assertStringContainsString(
            '/tasks/7%2Funsafe%3Fx%3D1',
            $response->body,
        );

        self::assertStringNotContainsString(
            'href="/tasks/7/unsafe?',
            $response->body,
        );
    }

    public function testNextPageLinkPreservesStatusAndEscapesAmpersand(): void
    {
        $response = self::views()->list(
            new TaskPage([
                self::task('42', 'Example', null),
            ], '42'),
            'completed',
        );

        self::assertStringContainsString(
            'href="/tasks?status=completed&amp;after=42"',
            $response->body,
        );

        self::assertStringContainsString(
            'aria-current="page">Completed',
            $response->body,
        );
    }

    public function testLastPageHasNoNextLink(): void
    {
        $response = self::views()->list(
            new TaskPage([
                self::task('42', 'Example', null),
            ], null),
        );

        self::assertStringNotContainsString(
            'rel="next"',
            $response->body,
        );
    }

    public function testEmptyListShowsMessage(): void
    {
        $response = self::views()->list(
            new TaskPage([], null),
        );

        self::assertStringContainsString(
            'No tasks found.',
            $response->body,
        );
    }

    public function testInvalidStatusFilterIsRejected(): void
    {
        $this->expectException(
            InvalidArgumentException::class,
        );

        self::views()->list(
            new TaskPage([], null),
            'completed<script>',
        );
    }

    public function testHtmlResponseHasSafeContentType(): void
    {
        $response = self::views()->detail(
            self::task('7', 'Example', null),
        );

        self::assertSame(200, $response->status);

        self::assertSame(
            'text/html; charset=UTF-8',
            $response->header('Content-Type'),
        );

        self::assertSame(
            'nosniff',
            $response->header('X-Content-Type-Options'),
        );
    }

    public function testProviderRegistersRenderer(): void
    {
        $container = new Container();

        new ViewServiceProvider()->register($container);

        self::assertInstanceOf(
            Environment::class,
            $container->get(Environment::class),
        );

        self::assertInstanceOf(
            TaskViews::class,
            $container->get(TaskViews::class),
        );
    }


    private static function views(): TaskViews
    {
        $twig = new Environment(
            new FilesystemLoader(
                dirname(__DIR__, 2) . '/resources/views',
            ),
            [
                'autoescape' => 'html',
                'strict_variables' => true,
                'cache' => false,
            ],
        );

        return new TaskViews(
            $twig,
            static fn (): string => str_repeat('a', 64),
        );
    }




    private static function task(
        string $id,
        string $title,
        ?string $description,
    ): Task {
        return new Task(
            $id,
            '3',
            $title,
            $description,
            'pending',
            '2026-11-07 12:00:00.000000',
            '2026-11-07 12:00:00.000000',
        );
    }
}
