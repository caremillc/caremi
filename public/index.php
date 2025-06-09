<?php declare(strict_types=1);

use Careminate\Http\Kernel;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

try {
    $request = Request::createFromGlobals();

    $kernel = new Kernel();
    $response = $kernel->handle($request);

    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    (new Response(
        "Internal Server Error:<br>" .
        nl2br($e->getMessage()) . "<br><br>" .
        nl2br($e->getTraceAsString()),
        500
    ))->send();
}
