<?php declare(strict_types=1);

$app = require_once __DIR__ . '/../bootstrap/app.php';

echo 'Careminate Framework booted successfully from: ' . base_path();

echo '<pre>';
echo 'App Name: ' . config('app.name') . PHP_EOL;
echo 'App Env: ' . config('app.env') . PHP_EOL;
echo 'App Debug: ' . var_export(config('app.debug'), true) . PHP_EOL;
echo 'DB Host: ' . config('database.connections.mysql.host') . PHP_EOL;
echo 'DB Name: ' . config('database.connections.mysql.database') . PHP_EOL;
echo '</pre>';