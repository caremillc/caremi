<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('home',['title'=>'home page']);
    }

    public function about()
    {
        return view('about',['title'=>'about page']);
    }
    public function contact()
    {
        return view('contact',['title'=>'contact page']); 
    }
}