<?php 
namespace App\Http;

use Careminate\Http\HttpKernel;

class Kernel extends HttpKernel
{
    public static $globalWeb = [
        \Careminate\Sessions\Session::class,
    ];
   
    public static $globalApi = [];
}