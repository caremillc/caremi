<?php

declare(strict_types=1);

use Careminate\Http\Routing\Router;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$file = dirname(__DIR__) . '/routes/web.php';

if (! is_file($file)) {
    throw new RuntimeException(
        'Application route file is missing.',
    );
}

$register = require $file;

if (! $register instanceof Closure) {
    throw new UnexpectedValueException(
        'Application route file must return a Closure.',
    );
}

$router = new Router();

$register($router);

return $router;
