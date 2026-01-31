<?php declare(strict_types=1);
namespace App\Console;

use App\Console\Commands\HelloCommand;

class Kernel
{
    /**
     * Register all user-defined commands here
     */
    public function commands(): array
    {
        return [
             new HelloCommand(),
            // new AnotherCommand(),
        ];
    }
}
