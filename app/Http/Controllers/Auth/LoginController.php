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
		dd($this->request->all());
    }
}