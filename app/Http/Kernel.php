<?php declare(strict_types=1);
namespace App\Http;

class Kernel
{
   // Global middleware for web routes
public static $globalWeb = [];

// Route-specific middleware for web routes
public static $middlewareWebRoute = [];

// Global middleware for API routes
public static $globalApi = [];

// Route-specific middleware for API routes
public static $middlewareApiRoute = [];
}
