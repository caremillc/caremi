<?php declare(strict_types=1);

use Careminate\Contracts\Http\KernelInterface;
use Careminate\Contracts\Http\RequestInterface;

$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var KernelInterface $kernel */
$kernel = $app->make(KernelInterface::class);

/** @var RequestInterface $request */
$request = $app->make(RequestInterface::class);

$kernel->send($request);