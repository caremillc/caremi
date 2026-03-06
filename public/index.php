<?php declare(strict_types=1);

$app = require_once __DIR__.'/../bootstrap/app.php';

require BASE_PATH . '/bootstrap/performance.php';

echo $app->run();