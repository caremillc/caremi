<?php

/**
 * View Configuration Settings
 *
 * This configuration file defines settings related to the handling of views, including the paths for templates,
 * the caching options, and cache expiry time.
 * 
 * Configuration keys:
 * - `path`: The directory where the view templates are stored.
 * - `cache_directory`: The directory where the compiled view cache will be stored.
 * - `cache`: A boolean that controls whether view caching is enabled (true to enable caching, false to disable).
 * - `cache_expiry`: The time in seconds before the cached views expire.
 * 
 * @return array Configuration array for view handling.
 */
return [
    'path'            => base_path('templates/views'),        // Define the path to the views directory.
    'cache_directory' => storage_path('views/'),              // Define the path where view cache files will be stored.
    'cache'           => false,                                // Set to true to enable view caching.
    'cache_expiry'    => 360                                   // Set the cache expiry time in seconds (360 seconds = 6 minutes).
];

 
