<?php declare(strict_types=1);

use Careminate\Http\HttpKernel;
use Careminate\Http\Requests\Request;

// require_once __DIR__.'/../bootstrap/app.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

require BASE_PATH . '/bootstrap/performance.php';


/*
|--------------------------------------------------------------------------
| Handle Request Through Kernel
|--------------------------------------------------------------------------
*/
$request = Request::capture();

// Better debug
// dd(
//     $request,
//     $request->method(),
//     $request->path(),
//     $request->all()
// );


$kernel = $app->make(HttpKernel::class);

// dd($kernel);

$response = $kernel->handle($request);

// $response->send();

$kernel->terminate($request, $response);


// dd($response);
// dd($request);
/*
|--------------------------------------------------------------------------
| Send Response
|--------------------------------------------------------------------------
*/

$response->send();