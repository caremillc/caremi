<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

echo "\nCaremi Framework Setup\n";
echo "----------------------\n";

/*
|--------------------------------------------------------------------------
| Create .env file
|--------------------------------------------------------------------------
*/

$envExample = $basePath.'/.env.example';
$envFile    = $basePath.'/.env';


if (!file_exists($envFile) && file_exists($envExample)) {
    copy($envExample, $envFile);
    echo "✔ .env created\n";
} else {
    echo "✔ .env already exists\n";
}

/*
|--------------------------------------------------------------------------
| Create bootstrap cache
|--------------------------------------------------------------------------
*/

$bootstrapCache = $basePath.'/bootstrap/cache';

if (!is_dir($bootstrapCache)) {
    mkdir($bootstrapCache, 0755, true);
    echo "✔ bootstrap/cache created\n";
}

/*
|--------------------------------------------------------------------------
| Create routes cache file
|--------------------------------------------------------------------------
*/

$routeCache = $bootstrapCache.'/routes.php';

if (!file_exists($routeCache)) {

    $content = "<?php\n\nreturn [\n    'static' => [],\n    'dynamic' => []\n];\n";

    file_put_contents($routeCache, $content);

    echo "✔ route cache file created\n";
}

/*
|--------------------------------------------------------------------------
| Create storage directories
|--------------------------------------------------------------------------
*/

$directories = [
    'storage',
    'storage/logs',
    'storage/cache',
    'storage/cache/views',
    'storage/cache/data',
    'storage/sessions',
    'storage/uploads'
];

foreach ($directories as $dir) {

    $path = $basePath.'/'.$dir;

    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "✔ {$dir} created\n";
    }
}


/*
|--------------------------------------------------------------------------
| Create log directories
|--------------------------------------------------------------------------
*/

$logFile = $basePath.'/storage/logs/app.log';

if (!file_exists($logFile)) {
    file_put_contents($logFile, '');
    echo "✔ log file created\n";
}


/*
|--------------------------------------------------------------------------
| Done
|--------------------------------------------------------------------------
*/

echo "\nCaremi installation completed successfully.\n\n";