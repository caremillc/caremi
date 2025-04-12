<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return 'Welcome to the home index page';
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