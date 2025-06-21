<?php 

use Careminate\Security\Encrypter;

$appKey = env('APP_KEY');
$encrypter = new Encrypter($appKey);

// Optional: bind to container or global helper
$GLOBALS['encrypter'] = $encrypter;