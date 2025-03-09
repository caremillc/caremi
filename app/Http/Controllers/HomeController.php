<?php
namespace App\Http\Controllers;

use Careminate\Http\Validations\Validate;

class HomeController extends Controller
{
    public function index()
    {  
	    var_dump(request());
        exit;
    }

}