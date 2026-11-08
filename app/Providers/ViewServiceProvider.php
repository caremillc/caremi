<?php

declare(strict_types=1);

namespace App\Providers;

use App\View\TaskViews;
use Careminate\Container\Container;
use Careminate\Foundation\ServiceProvider;
use Careminate\Security\CsrfTokenManager;
use LogicException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class ViewServiceProvider implements ServiceProvider
{
    public function register(Container $container): void
    {
        $directory = dirname(__DIR__, 2) . '/resources/views';

        $container->singleton(
            Environment::class,
            static fn(Container $container): Environment => new Environment(
                new FilesystemLoader($directory),
                [
                    'autoescape' => 'html',
                    'strict_variables' => true,
                    'cache' => false,
                ],
            ),
        );

        $container->singleton(
            TaskViews::class,
            static function (Container $container): TaskViews {
                $renderer = $container->get(Environment::class);

                if (! $renderer instanceof Environment) {
                    throw new LogicException(
                        'Twig environment is unavailable.',
                    );
                }

                return new TaskViews(
                    $renderer,
                    static function () use ($container): string {
                        $csrf = $container->get(CsrfTokenManager::class);

                        if (! $csrf instanceof CsrfTokenManager) {
                            throw new LogicException(
                                'CSRF token manager unavailable.'
                            );
                        }

                        return $csrf->token();
                    },
                );
            },
        );
    }

    public function boot(Container $container): void
    {
        // No side effects needed during application boot.
    }
}
