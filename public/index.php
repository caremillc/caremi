<?php declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

// ✅ Autoload first
require_once BASE_PATH . '/vendor/autoload.php';

// ✅ Load .env before bootstrap
Dotenv\Dotenv::createImmutable(BASE_PATH)->safeLoad();

// ✅ Then bootstrap (safe to use env now)
require_once __DIR__ . '/../bootstrap/app.php';

use Careminate\Http\Responses\Response;

$response = new Response('Hello World! from index');
$response->send();
