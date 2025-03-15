<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        echo 'Welcome to the home index page';
    }

    public function about()
    {
        return 'Welcome to the home about page'; 
    }

    public function contact()
    {
        return 'Welcome to the home contact page'; 
    }
}
