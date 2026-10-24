<?php 

declare(strict_types=1);

use Careminate\Foundation\Application;
use Careminate\Http\Kernel;
use Careminate\Http\Middleware\ExceptionHandler;


$kernel = new Kernel(new Application());

$kernel->middleware(new ExceptionHandler());
