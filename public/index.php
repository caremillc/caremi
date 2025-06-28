<?php declare(strict_types=1);

use Careminate\Http\Responses\Response;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

define('APP_START', microtime(true));

require BASE_PATH . '/bootstrap/app.php';

// Test env access
$name = env('APP_NAME');
$key  = env('APP_KEY');

// echo encrypter()->encrypt('sensitive data');
// exit;

// Output or use in response
$response = Response::json([
    'name' => $name,
    'key' => $key,
]);
$response->send();


