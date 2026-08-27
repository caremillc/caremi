<?php

declare(strict_types=1);

namespace App\Billing\Contracts;

interface PaymentGateway
{
    public function charge(int $userId, int $amountInMinorUnits): string;
}