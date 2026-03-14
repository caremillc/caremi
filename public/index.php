<?php declare(strict_types=1);

use Careminate\Http\Kernel;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

/*================================
Responsibilities:

1.Load application bootstrap
2.Capture HTTP request
3.Send request to Kernel
4.Return response
=================================
*/


$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Performance bootstrap (optional)
|--------------------------------------------------------------------------
*/

require BASE_PATH . '/bootstrap/performance.php';

/*
|--------------------------------------------------------------------------
| Capture Request
|--------------------------------------------------------------------------
*/

$request = Request::capture();
// dd($request->method());
// dd($request->path());
/*
|--------------------------------------------------------------------------
| Handle Request Through Kernel
|--------------------------------------------------------------------------
*/

$kernel = $app->make(Kernel::class);

$response = $kernel->handle($request);
// dd($response);
/*
|--------------------------------------------------------------------------
| Send Response
|--------------------------------------------------------------------------
*/

$response->send();

/*
|--------------------------------------------------------------------------
| Terminate Application
|--------------------------------------------------------------------------
*/

$kernel->terminate($request, $response);


