<?php

declare(strict_types=1);

namespace App\Tests\Unit\Architecture;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class WorkspaceStructureTest extends TestCase
{
    private const array REQUIRED_DIRECTORIES = [
        'app',
        'bootstrap',
        'config',
        'docs',
        'framework',
        'framework/docs',
        'framework/src',
        'framework/tests',
        'modules',
        'public',
        'resources',
        'routes',
        'storage',
        'storage/cache',
        'storage/logs',
        'storage/runtime',
        'tests',
    ];

    private const array REQUIRED_FILES = [
        '.editorconfig',
        '.env.example',
        '.gitattributes',
        '.gitignore',
        'composer.json',
        'framework/composer.json',
        'framework/phpstan.neon',
        'framework/phpunit.xml',
        'framework/rector.php',
        'framework/phpcs.xml',
        'phpstan.neon',
        'phpunit.xml',
        'rector.php',
        'phpcs.xml',
    ];

    public function testRequiredWorkspaceDirectoriesExist(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (self::REQUIRED_DIRECTORIES as $directory) {
            self::assertDirectoryExists(
                $root . DIRECTORY_SEPARATOR . str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $directory
                ),
                sprintf(
                    'Required workspace directory "%s" does not exist.',
                    $directory
                )
            );
        }
    }

    public function testRequiredWorkspaceFilesExist(): void
    {
        $root = dirname(__DIR__, 3);

        foreach (self::REQUIRED_FILES as $file) {
            self::assertFileExists(
                $root . DIRECTORY_SEPARATOR . str_replace(
                    '/',
                    DIRECTORY_SEPARATOR,
                    $file
                ),
                sprintf(
                    'Required workspace file "%s" does not exist.',
                    $file
                )
            );
        }
    }

    public function testApplicationAndFrameworkHaveSeparateComposerManifests(): void
    {
        $root = dirname(__DIR__, 3);

        $applicationManifest = realpath(
            $root . DIRECTORY_SEPARATOR . 'composer.json'
        );

        $frameworkManifest = realpath(
            $root
            . DIRECTORY_SEPARATOR
            . 'framework'
            . DIRECTORY_SEPARATOR
            . 'composer.json'
        );

        self::assertNotFalse($applicationManifest);
        self::assertNotFalse($frameworkManifest);
        self::assertNotSame($applicationManifest, $frameworkManifest);
    }

    public function testFrameworkPackageDoesNotDependOnApplicationPackage(): void
    {
        $root = dirname(__DIR__, 3);

        $manifestPath = $root
            . DIRECTORY_SEPARATOR
            . 'framework'
            . DIRECTORY_SEPARATOR
            . 'composer.json';

        $contents = file_get_contents($manifestPath);

        self::assertNotFalse($contents);

        $manifest = json_decode(
            $contents,
            true,
            flags: JSON_THROW_ON_ERROR
        );

        self::assertIsArray($manifest);

        $requirements = $manifest['require'] ?? [];

        self::assertIsArray($requirements);

        self::assertArrayNotHasKey(
            'caremillc/caremi',
            $requirements,
            'The framework package must never depend on the reference application.'
        );
    }

    public function testApplicationUsesFrameworkPathRepository(): void
    {
        $root = dirname(__DIR__, 3);

        $contents = file_get_contents(
            $root . DIRECTORY_SEPARATOR . 'composer.json'
        );

        self::assertNotFalse($contents);

        $manifest = json_decode(
            $contents,
            true,
            flags: JSON_THROW_ON_ERROR
        );

        self::assertIsArray($manifest);

        $repositories = $manifest['repositories'] ?? [];

        self::assertIsArray($repositories);

        foreach ($repositories as $repository) {
            if (!is_array($repository)) {
                continue;
            }

            if (
                ($repository['type'] ?? null) === 'path'
                && ($repository['url'] ?? null) === 'framework'
            ) {
                self::assertTrue(true);

                return;
            }
        }

        self::fail(
            'The root Composer manifest must define "framework" as a path repository.'
        );
    }
}
