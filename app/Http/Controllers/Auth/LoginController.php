<?php declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repository\UserRepository;
use Careminate\Authentication\SessionAuthentication;
use Careminate\Http\Responses\Response;

class LoginController extends Controller
{
    public function __construct(
        private SessionAuthentication $auth,
        private UserRepository $userRepository) {}
    
    public function loginForm(): Response
    {
        return view('auth/login.html.twig');
    }

    public function login(): Response
    {
        $email    = $this->request->input('email');
        $password = $this->request->input('password');

        // ✅ Check if user exists before attempting authentication
        $user = $this->userRepository->findByEmail($email);
      
        if (! $user) {
            flash('error', 'User does not exist');
            return redirect('/login');
        }

        // ✅ Attempt to authenticate the user
        if (! $this->auth->authenticate($email, $password)) {
            flash('error', 'Invalid credentials');
            return redirect('/login');
        }

        flash('success', 'You are now logged in');
        return view('admin/dashboard.html.twig', ['user' => $user]);
    }

     public function logout(): Response
    {
        // Log the user out
        $this->auth->logout();

        // Set a logout session message
        flash('success', 'Bye..see you soon!');

        // Redirect to login page
        return redirect('/login');
    }
}
