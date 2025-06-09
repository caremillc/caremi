<?php 
namespace App\Http\Controllers;

class InvokeController extends Controller
{
    public function __invoke()
    {
        echo 'this is invoke controller';
    }
}