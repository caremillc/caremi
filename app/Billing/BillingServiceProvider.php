<?php

declare(strict_types=1);

namespace App\Billing;

use App\Billing\Contracts\PaymentGateway;
use App\Billing\Infrastructure\NullPaymentGateway;
use Careminate\Contracts\Module\ServiceProviderInterface;
use Careminate\Module\ModuleContext;

final class BillingServiceProvider implements ServiceProviderInterface
{
    public function register(ModuleContext $context): void
    {
        $context->services->singleton(
            PaymentGateway::class,
            NullPaymentGateway::class,
        );
    }

    public function boot(ModuleContext $context): void
    {
    }
}
