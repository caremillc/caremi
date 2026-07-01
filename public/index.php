<?php 

use Careminate\Foundation\Application;

require_once __DIR__.'/../bootstrap/app.php';
$app = new Application(dirname(__DIR__));

echo"<pre>";
echo 'App Vision '. $app->version().PHP_EOL;
echo 'Get App UserController '. $app->appPath('Http/Controllers/UserController.php').PHP_EOL;
echo 'Get Cofig File '. $app->configPath('app.php').PHP_EOL;
echo 'App Storage Path '. $app->storagePath('logs/app.log').PHP_EOL;