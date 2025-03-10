<?php
namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {  
	    // var_dump(request());
        // var_dump(Request::get('user_id'));
		// var_dump(request('user_id'));
        // exit;
        var_dump(new User());
        return view('home',['title'=>'Home page']);
    }

}