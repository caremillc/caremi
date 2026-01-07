<?php

namespace Careminate\Console\Commands;

use Careminate\Console\Commands\Command;

class CommandName extends Command
{
    protected string $signature = 'CommandName';
    protected string $description = 'Describe your command';

    public function handle(array $arguments=[]): int
    {
        echo "CommandName executed!\n";

        return 0;
    }
}