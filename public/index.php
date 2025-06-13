<?php declare(strict_types=1);

use Careminate\Views\View;
use Careminate\Http\Kernel;
use Careminate\Support\Config;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;
use Careminate\Exceptions\AuthorizationException;
use Careminate\Exceptions\ModelNotFoundException;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

try {
    $request = Request::createFromGlobals();

    $kernel = new Kernel();
    $response = $kernel->handle($request);

} catch (ModelNotFoundException $e) {
    $view = Config::get('errors.404', 'errors/404');
    $response = new Response(View::make($view, ['message' => $e->getMessage()]), 404);
   
} catch (AuthorizationException $e) {
    $view = Config::get('errors.403', 'errors/403');
    $response = new Response(View::make($view, ['message' => $e->getMessage()]), 403);
    
} catch (Throwable $e) {
    $view = Config::get('errors.500', 'errors/500');
    $response = new Response(View::make($view, ['message' => $e->getMessage()]), 500);
    
}catch (Throwable $e) {
    (new Response(
        "Internal Server Error:<br>" .
        nl2br($e->getMessage()) . "<br><br>" .
        nl2br($e->getTraceAsString()),
        500
    ))->send();
}

$response->send();
// $response->send();
$kernel->terminate($request, $response);