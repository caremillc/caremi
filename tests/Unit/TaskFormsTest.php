<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Persistence\TaskRepository;
use App\View\TaskFormHandler;
use App\View\TaskFormValidator;
use App\View\TaskViews;
use Careminate\Database\SqlConnection;
use Careminate\Database\SqlResult;
use Closure;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TaskFormsTest extends TestCase
{
    public function testNewFormShowsPendingState(): void
    {
        [$handler] = self::setupHandler();

        $response = $handler->newForm();

        self::assertSame(200, $response->status);
        self::assertStringContainsString(
            'name="title"',
            $response->body,
        );
        self::assertStringContainsString(
            'name="status" value="pending"',
            $response->body,
        );
        self::assertSame(
            'no-store',
            $response->header('Cache-Control'),
        );

        self::assertStringContainsString(
            'name="_token" value="' . str_repeat('a', 64) . '"',
            $response->body,
        );
    }

    public function testInvalidCreatePreservesEscapedValuesAndDoesNotWrite(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->createForUser('7', [
            'title' => '<script>alert(1)</script>',
            'description' => '<b>details</b>',
            'status' => 'completed',
        ]);

        self::assertSame(422, $response->status);
        self::assertStringContainsString(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            $response->body,
        );
        self::assertStringContainsString(
            '&lt;b&gt;details&lt;/b&gt;',
            $response->body,
        );
        self::assertStringContainsString(
            'New tasks must start as pending.',
            $response->body,
        );
        self::assertStringNotContainsString(
            '<script>',
            $response->body,
        );
        self::assertSame([], $db->statements);
    }

    public function testBlankTitleIsRejected(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->createForUser('7', [
            'title' => '   ',
            'status' => 'pending',
        ]);

        self::assertSame(422, $response->status);
        self::assertStringContainsString(
            'Title must contain 1 to 200 characters.',
            $response->body,
        );
        self::assertSame([], $db->statements);
    }

    public function testCreateRejectsMalformedFieldsBeforeSql(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->createForUser('7', [
            'title' => ['wrong'],
            'description' => str_repeat('x', 65536),
            'status' => 'pending',
        ]);

        self::assertSame(422, $response->status);
        self::assertStringContainsString(
            'This field must be text.',
            $response->body,
        );
        self::assertStringContainsString(
            'Description is too long',
            $response->body,
        );
        self::assertSame([], $db->statements);
    }

    public function testCreateRequiresStatus(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->createForUser(
            '7',
            ['title' => 'Example'],
        );

        self::assertSame(422, $response->status);
        self::assertSame([], $db->statements);
    }

    public function testCreateRejectsInvalidUtf8WithoutEchoingIt(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->createForUser('7', [
            'title' => "\xC3\x28",
            'status' => 'pending',
        ]);

        self::assertSame(422, $response->status);
        self::assertStringContainsString(
            'valid UTF-8',
            $response->body,
        );
        self::assertSame([], $db->statements);
    }

    public function testValidCreateBindsNormalizedValuesAndRedirects(): void
    {
        [$handler, $db] = self::setupHandler();

        $db->results = [
            new SqlResult([], 1),
            new SqlResult([self::row('Clean', null)], 0),
        ];

        $response = $handler->createForUser('7', [
            'title' => '  Clean  ',
            'description' => '',
            'status' => 'pending',
        ]);

        self::assertSame(303, $response->status);
        self::assertSame(
            '/tasks/42',
            $response->header('Location'),
        );
        self::assertSame(
            [
                'user_id' => '7',
                'title' => 'Clean',
                'description' => null,
            ],
            $db->statements[0]['parameters'],
        );
    }

    public function testEditFormPrefillsEscapedContent(): void
    {
        [$handler, $db] = self::setupHandler();

        $db->results = [
            new SqlResult([
                self::row(
                    '<script>alert(1)</script>',
                    '<b>notes</b>',
                ),
            ], 0),
        ];

        $response = $handler->editForUser('7', '42');

        self::assertSame(200, $response->status);
        self::assertStringContainsString(
            'action="/tasks/42"',
            $response->body,
        );
        self::assertStringContainsString(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            $response->body,
        );
        self::assertStringContainsString(
            '&lt;b&gt;notes&lt;/b&gt;',
            $response->body,
        );
        self::assertStringNotContainsString(
            '<script>',
            $response->body,
        );

        self::assertStringContainsString(
            'name="_token" value="' . str_repeat('a', 64) . '"',
            $response->body,
        );
    }

    public function testForeignOrMissingEditIsNotFound(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->editForUser('8', '42');

        self::assertSame(404, $response->status);
        self::assertSame('Not found.', $response->body);
        self::assertCount(1, $db->statements);
        self::assertSame(
            ['user_id' => '8', 'id' => '42'],
            $db->statements[0]['parameters'],
        );
    }

    public function testInvalidUpdatePreservesValuesAndDoesNotMutate(): void
    {
        [$handler, $db] = self::setupHandler();
        $db->results = [
            new SqlResult([self::row()], 0),
        ];

        $response = $handler->updateForUser('7', '42', [
            'title' => 'Changed',
            'description' => '<em>keep this</em>',
            'status' => 'archived',
        ]);

        self::assertSame(422, $response->status);
        self::assertStringContainsString(
            'value="Changed"',
            $response->body,
        );
        self::assertStringContainsString(
            '&lt;em&gt;keep this&lt;/em&gt;',
            $response->body,
        );
        self::assertStringContainsString(
            'Select a valid task status.',
            $response->body,
        );
        self::assertCount(1, $db->statements);
    }

    public function testValidUpdateReachesRepositoryAndRedirects(): void
    {
        [$handler, $db] = self::setupHandler();

        $db->results = [
            new SqlResult([self::row()], 0),
            new SqlResult([], 1),
            new SqlResult([
                self::row('Updated', 'Notes', 'completed'),
            ], 0),
        ];

        $response = $handler->updateForUser('7', '42', [
            'title' => '  Updated  ',
            'description' => 'Notes',
            'status' => 'completed',
        ]);

        self::assertSame(303, $response->status);
        self::assertSame(
            '/tasks/42',
            $response->header('Location'),
        );
        self::assertCount(3, $db->statements);
        self::assertSame(
            [
                'title' => 'Updated',
                'description' => 'Notes',
                'status' => 'completed',
                'user_id' => '7',
                'id' => '42',
            ],
            $db->statements[1]['parameters'],
        );
    }

    public function testMissingTaskUpdateDoesNotMutate(): void
    {
        [$handler, $db] = self::setupHandler();

        $response = $handler->updateForUser('8', '42', [
            'title' => 'Changed',
            'status' => 'completed',
        ]);

        self::assertSame(404, $response->status);
        self::assertCount(1, $db->statements);
    }

    /** @return array{TaskFormHandler, Day32SqlSpy} */
    private static function setupHandler(): array
    {
        $database = new Day32SqlSpy();

        $views = new TaskViews(
            new Environment(
                new FilesystemLoader(
                    dirname(__DIR__, 2) . '/resources/views',
                ),
                [
                    'autoescape' => 'html',
                    'strict_variables' => true,
                    'cache' => false,
                ],
            ),
            static fn (): string => str_repeat('a', 64),
        );

        return [
            new TaskFormHandler(
                new TaskRepository($database),
                $views,
                new TaskFormValidator(),
            ),
            $database,
        ];
    }

    /** @return array<string, mixed> */
    private static function row(
        string $title = 'Original',
        ?string $description = null,
        string $status = 'pending',
    ): array {
        return [
            'id' => '42',
            'user_id' => '7',
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'created_at' => '2026-11-07 10:20:30.000000',
            'updated_at' => '2026-11-07 10:20:30.000000',
        ];
    }
}

/** Recording SQL connection for isolated application unit tests. */
final class Day32SqlSpy implements SqlConnection
{
    /** @var list<SqlResult> */
    public array $results = [];

    /**
     * @var list<array{
     *     sql: string,
     *     parameters: array<int|string, mixed>
     * }>
     */
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
        return '42';
    }
}
