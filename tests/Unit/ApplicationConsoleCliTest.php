<?php

declare(strict_types=1);

namespace Tests\Unit;

use Careminate\Foundation\Framework;
use PHPUnit\Framework\TestCase;

final class ApplicationConsoleCliTest extends TestCase
{
    public function testApplicationHelpAdvertisesCommands(): void
    {
        [$code, $stdout, $stderr] = self::invoke(['--help']);

        self::assertSame(0, $code, $stderr);
        self::assertStringContainsString('route:list', $stdout);
        self::assertStringContainsString('about', $stdout);
        self::assertSame('', $stderr);
    }

    public function testVersionUsesFrameworkVersion(): void
    {
        [$code, $stdout, $stderr] = self::invoke(['--version']);

        self::assertSame(0, $code, $stderr);

        self::assertSame(
            'Careminate ' . Framework::version() . "\n",
            $stdout,
        );

        self::assertSame('', $stderr);
    }

    public function testRouteListTableUsesApplicationRoutes(): void
    {
        [$code, $stdout, $stderr] = self::invoke(['route:list']);

        self::assertSame(0, $code, $stderr);
        self::assertStringContainsString('/api/items/{id}', $stdout);
        self::assertStringContainsString('api.items.show', $stdout);
        self::assertStringContainsString('/health', $stdout);
        self::assertSame('', $stderr);
    }

    public function testRouteListJsonHasExpectedRows(): void
    {
        [$code, $stdout, $stderr] = self::invoke([
            'route:list',
            '--format=json',
        ]);

        self::assertSame(0, $code, $stderr);
        self::assertSame('', $stderr);

        $decoded = json_decode(
            $stdout,
            true,
            flags: JSON_THROW_ON_ERROR,
        );

        self::assertIsArray($decoded);
        // self::assertCount(4, $decoded);
        self::assertCount(6, $decoded);

        self::assertContains(
            [
                'method' => 'GET',
                'path' => '/health',
                'name' => 'health',
            ],
            $decoded,
        );
    }

    public function testInvalidFormatReturns64(): void
    {
        [$code, $stdout, $stderr] = self::invoke([
            'route:list',
            '--format=xml',
        ]);

        self::assertSame(64, $code, $stderr);
        self::assertSame('', $stdout);
        self::assertStringContainsString('Usage:', $stderr);
    }

    public function testAboutRunsWithoutExposingEnvironmentSecret(): void
    {
        $name = 'CAREMINATE_CLI_TEST_SECRET';
        $previous = getenv($name);
        $secret = 'CAREMINATE_PRIVATE_54319';

        putenv($name . '=' . $secret);

        try {
            [$code, $stdout, $stderr] = self::invoke([
                'about',
                '--format=json',
            ]);

            self::assertSame(0, $code, $stderr);

            self::assertStringContainsString(
                Framework::version(),
                $stdout,
            );

            self::assertStringNotContainsString(
                $secret,
                $stdout,
            );

            self::assertStringNotContainsString(
                $secret,
                $stderr,
            );
        } finally {
            putenv(
                $previous === false
                    ? $name
                    : $name . '=' . $previous,
            );
        }
    }

    public function testRouteListWorksOutsideWorkingDirectory(): void
    {
        [$code, $stdout, $stderr] = self::invoke(
            ['route:list', '--format=json'],
            sys_get_temp_dir(),
        );

        self::assertSame(0, $code, $stderr);
        self::assertStringContainsString('/api/status', $stdout);
        self::assertSame('', $stderr);
    }

    public function testUnknownCommandDoesNotEchoUntrustedText(): void
    {
        [$code, $stdout, $stderr] = self::invoke([
            "missing\nPRIVATE\e[31m",
        ]);

        self::assertSame(64, $code, $stderr);
        self::assertSame('', $stdout);
        self::assertStringNotContainsString('PRIVATE', $stderr);
        self::assertStringNotContainsString("\e", $stderr);
    }

    /**
     * @param list<string> $arguments
     * @return array{int, string, string}
     */
    private static function invoke(
        array $arguments,
        ?string $workingDirectory = null,
    ): array {
        $root = dirname(__DIR__, 2);
        $entrypoint = $root . '/careminate';

        self::assertFileExists($entrypoint);

        /** @var array<int, resource> $pipes */
        $pipes = [];

        $process = proc_open(
            [PHP_BINARY, $entrypoint, ...$arguments],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $workingDirectory ?? $root,
            null,
            ['bypass_shell' => true],
        );

        if (! is_resource($process)) {
            self::fail(
                'Could not start application CLI subprocess.',
            );
        }

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $code = proc_close($process);

        self::assertIsString($stdout);
        self::assertIsString($stderr);

        return [$code, $stdout, $stderr];
    }
}
