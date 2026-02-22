<?php declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;

class LoginController extends Controller
{
    public function loginForm(): Response
    {
        return view('auth/login.html.twig');
    }

    public function login(): Response
    {
        // Attempt to authenticate the user using a security component (bool)
       // dd($this->request);
		dd($this->request->all());
        // create a session for the user

        // If successful, retrieve the user

        // Redirect the user to intended location
        return redirect('/');
    }
}