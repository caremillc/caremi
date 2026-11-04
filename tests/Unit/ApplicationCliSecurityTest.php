<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ApplicationCliSecurityTest extends TestCase
{
    /**
     * @return iterable<string, array{list<string>}>
     */
    public static function invalidRouteOptions(): iterable
    {
        yield 'unknown format' => [
            ['--format=xml'],
        ];

        yield 'unsupported flag' => [
            ['--all'],
        ];

        yield 'duplicate format' => [
            ['--format=json', '--format=json'],
        ];

        yield 'split format' => [
            ['--format', 'json'],
        ];

        yield 'terminal escape and secret' => [
            ["\e[31mPRIVATE\n"],
        ];
    }

    /**
     * @param list<string> $arguments
     */
    #[DataProvider('invalidRouteOptions')]
    public function testInvalidRouteOptionsReturnSafeUsageErrors(
        array $arguments,
    ): void {
        [$code, $stdout, $stderr] = self::invoke([
            'route:list',
            ...$arguments,
        ]);

        self::assertSame(64, $code, $stderr);
        self::assertSame('', $stdout);

        self::assertStringContainsString(
            'Usage:',
            $stderr,
        );

        self::assertStringNotContainsString(
            'PRIVATE',
            $stderr,
        );

        self::assertStringNotContainsString(
            "\e",
            $stderr,
        );
    }

    public function testRouteListStillWorksAfterUsageError(): void
    {
        [$badCode] = self::invoke([
            'route:list',
            '--bad',
        ]);

        [$code, $stdout, $stderr] = self::invoke([
            'route:list',
            '--format=json',
        ]);

        self::assertSame(64, $badCode);
        self::assertSame(0, $code, $stderr);
        self::assertSame('', $stderr);

        self::assertStringContainsString(
            '"/api/status"',
            $stdout,
        );
    }

    public function testUnknownCommandNeverEchoesTerminalControlInput(): void
    {
        [$code, $stdout, $stderr] = self::invoke([
            "UNTRUSTED\e[31mPRIVATE\n",
        ]);

        self::assertSame(64, $code, $stderr);
        self::assertSame('', $stdout);

        self::assertStringNotContainsString(
            'PRIVATE',
            $stderr,
        );

        self::assertStringNotContainsString(
            "\e",
            $stderr,
        );
    }

    public function testSafeAboutOutputRemainsAvailable(): void
    {
        [$code, $stdout, $stderr] = self::invoke([
            'about',
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

        $framework = $decoded['framework'] ?? null;

        self::assertIsArray($framework);

        self::assertSame(
            'Careminate',
            $framework['name'] ?? null,
        );
    }

    /**
     * @param list<string> $arguments
     * @return array{int, string, string}
     */
    private static function invoke(
        array $arguments,
    ): array {
        $root = dirname(__DIR__, 2);

        $entrypoint = $root
            . DIRECTORY_SEPARATOR
            . 'careminate';

        self::assertFileExists($entrypoint);

        /** @var array<int, resource> $pipes */
        $pipes = [];

        $process = proc_open(
            [
                PHP_BINARY,
                $entrypoint,
                ...$arguments,
            ],
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            $root,
            null,
            ['bypass_shell' => true],
        );

        if (! is_resource($process)) {
            self::fail(
                'Cannot start application CLI.',
            );
        }

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        self::assertIsString($stdout);
        self::assertIsString($stderr);

        return [
            $exitCode,
            $stdout,
            $stderr,
        ];
    }
}
