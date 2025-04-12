<?php 
namespace App\Http;

use Careminate\Core;

class Kernel 
{
    public static $globalWeb = [
        \Careminate\Sessions\Session::class,
    ];
   
    public static $globalApi = [];
}