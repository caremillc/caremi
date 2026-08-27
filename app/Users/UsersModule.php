<?php

declare(strict_types=1);

namespace App\Users;

use App\Users\Contracts\UserRepository;
use App\Users\Infrastructure\InMemoryUserRepository;
use Careminate\Contracts\Module\ModuleInterface;
use Careminate\Module\ModuleContext;
use Careminate\Module\ModuleDefinition;

final class UsersModule implements ModuleInterface
{
    public static function definition(): ModuleDefinition
    {
        return ModuleDefinition::named('users')
            ->version('1.0.0')
            ->provides(UserRepository::class);
    }

    public function register(ModuleContext $context): void
    {
        $context->services->singleton(
            UserRepository::class,
            InMemoryUserRepository::class,
        );
    }

    public function boot(ModuleContext $context): void
    {
    }
}
