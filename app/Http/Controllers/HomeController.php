<?php
namespace App\Http\Controllers;

use App\Models\Auth\User;
use Careminate\Databases\Model;

class HomeController extends Controller
{
    public function index()
		{  
			$user = new User();
			$user->name = "John Joe";
			var_dump($user->name);
		} 
}