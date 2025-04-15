<?php

namespace App\Console\Commands\Admin;

use Careminate\Support\Console\Display;
use Careminate\Console\Commands\Contracts\CommandInterface;


class NotifyUsersCommand implements CommandInterface
{
    use Display;

    public function signature(): string
    {
        return 'notifyusers:custom';
    }

    public function description(): string
    {
        return 'Description for NotifyUsersCommand';
    }

    public function handle(array $arguments = []): void
    {
        $this->green("NotifyUsersCommand command executed.");
    }
}
