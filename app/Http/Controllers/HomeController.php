<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        echo 'welcome to home index page';
    }

    public function about()
    {
        echo 'welcome to home about page'; 
    }
    public function contact()
    {
        echo 'welcome to home contact page'; 
    }
}