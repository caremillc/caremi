<?php
return [
    'path'            => base_path('templates'),        // Define the path to the views directory.
    'cache_directory' => storage_path('views'),              // Define the path where view cache files will be stored.
    'cache'           => true,                                // Set to true to enable view caching.
    'cache_expiry'    => 360                                   // Set the cache expiry time in seconds (360 seconds = 6 minutes).
];