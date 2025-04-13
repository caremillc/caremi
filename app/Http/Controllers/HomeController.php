<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'page title';
        return view('home',compact('title'));
    }

    public function about()
    {
        return 'Welcome to the home about page';
    }

    public function articles($id, $slug)
    {
        return "This is the article with ID $id and slug $slug";
    }
}