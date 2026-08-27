<?php

declare(strict_types=1);

namespace App\Billing;

use App\Billing\Contracts\PaymentGateway;
use App\Users\Contracts\UserRepository;
use App\Users\UsersModule;
use Careminate\Contracts\Module\ModuleInterface;
use Careminate\Module\ModuleContext;
use Careminate\Module\ModuleDefinition;

final class BillingModule implements ModuleInterface
{
    public static function definition(): ModuleDefinition
    {
        return ModuleDefinition::named('billing')
            ->version('1.0.0')
            ->requires(UsersModule::class, '^1.0')
            ->requiresCapability(UserRepository::class)
            ->provides(PaymentGateway::class)
            ->provider(BillingServiceProvider::class);
    }

    public function register(ModuleContext $context): void
    {
    }

    public function boot(ModuleContext $context): void
    {
    }
}