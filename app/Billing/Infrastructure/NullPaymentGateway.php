<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure;

use App\Billing\Contracts\PaymentGateway;
use App\Users\Contracts\UserRepository;
use DomainException;

final readonly class NullPaymentGateway implements PaymentGateway
{
    public function __construct(private UserRepository $users)
    {
    }

    public function charge(int $userId, int $amountInMinorUnits): string
    {
        if (!$this->users->exists($userId)) {
            throw new DomainException(sprintf(
                'User "%d" does not exist.',
                $userId,
            ));
        }

        if ($amountInMinorUnits < 1) {
            throw new DomainException(
                'The payment amount must be greater than zero.',
            );
        }

        return sprintf('test-payment-%d-%d', $userId, $amountInMinorUnits);
    }
}
