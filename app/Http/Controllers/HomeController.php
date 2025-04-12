<?php 
namespace App\Http\Controllers;

class HomeController extends Controller 
{

    public function index()
    {
        echo "welcome to home page";
    }

    public function about()
    {
        echo "welcome to about page";
    }
}