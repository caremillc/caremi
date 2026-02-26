<?php declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Entity\Register;
use App\Http\Controllers\Controller;
use App\Repository\UserMapper;
use App\Repository\UserRepository;
use Careminate\Authentication\SessionAuthentication;
use Careminate\Http\Responses\Response;

class RegistrationController extends Controller
{
    // public function __construct(private UserMapper $userMapper){}
     public function __construct(
        private UserMapper $userMapper,
        private UserRepository $userRepository,
        private SessionAuthentication $auth
    ) {}

    public function index(): Response
    {
        return view('auth/register.html.twig');
    }

    public function register(): Response
    {
        // Create a form model which will:
        // - validate fields
        // - map the fields to User object properties
        // - ultimately save the new User to the DB
        $user = new Register($this->userMapper);
        $user->setFields(
            $this->request->input('username'),
            $this->request->input('email'),
            $this->request->input('role'),
            $this->request->input('password')
        );

        // Validate
        // If validation errors,
        // add to session, redirect to form
        // Check if there are validation errors
        if ($user->hasValidationErrors()) {
            // Retrieve the validation errors
            $errors = $user->getValidationErrors();

            // Display errors to the user (this is an example; in a real application, you might use a view template)
            foreach ($errors as $error) {
                flash('error', $error);
            }

            // Optionally, you might re-render the form here for the user to correct their input
            return redirect('/register');
        }
        // register the user by calling $form->save()

        $user = $user->save();
        
        // dd($user); 
        // Add a session success message
        flash('success', sprintf('User successfully created'));
        // $this->request->getSession()->setFlash(
        //     'success',
        //     sprintf('User %s created', $user->getUsername())
        // );
        // Log the user in
          $this->auth->login($user);
        // Redirect to somewhere useful
        return redirect('admin/dashboard');
    }

}


