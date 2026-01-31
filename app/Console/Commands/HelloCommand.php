<?php declare(strict_types=1);

namespace App\Console\Commands;

use Careminate\Console\Commands\AbstractCommand;

class HelloCommand extends AbstractCommand
{
    protected string $signature = 'HelloCommand';
    protected string $description = 'Say Hello from HelloCommand';

    public function handle(array $arguments = []): int
    {
        echo "HelloCommand successfully executed!\n";
        return 0;
    }
}