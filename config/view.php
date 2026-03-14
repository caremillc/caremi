<?php declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | View Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [BASE_PATH . '/resources/views',],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */

    // 'compiled' => env('VIEW_COMPILED_PATH', BASE_PATH . '/storage/cache/views'),
    'compiled' => BASE_PATH . '/' . env('VIEW_COMPILED_PATH', 'storage/cache/views'),

];