<?php declare(strict_types=1);
namespace App\Http\Controllers;

class HomeController extends Controller 
{
    public function index(): void
    {
        echo "Welcome to home index";
    }

    public function about(): void
    {
        echo "Welcome to home about";
    }

      public function article($id,$slug=''): void
    {
        echo "Welcome to home about '.$id.' and slug '.$slug.'";
    }
}
