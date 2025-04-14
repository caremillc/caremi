<?php

namespace App\Http\Controllers;

use Careminate\Requests\Request;
use Careminate\Http\Responses\Response;
use Careminate\Http\Validations\Validate;

class HomeController extends Controller
{
    public function index()
    {
        $requests = [
            'name'                  => $_POST['name'] ?? 'eric',
            'age'                   => $_POST['age'] ?? 20,
        //    'image'                  => $_FILES['image'] ?? null,
            'password'              => $_POST['password'] ?? 'password',
            'password_confirmation' => $_POST['password_confirmation'] ?? 'password',
            'email'                 => $_POST['email'] ?? 'eric@gmail.com',
            'category_id'           => $_POST['name'] ?? 1,
        ];

        $validation = Validate::make($requests,[
                'name'     => 'required|string|min:3|unique:users',
                'age'      => 'required|integer|min:18',
                // 'image'    => 'required|file',
                'password' => 'required|confirmed',
                'email' => 'required|email|unique:users,email',
                'category_id' => 'required|exists:categories,id'
            ],
            [
                'name'     => 'Full Name',
                'age'      => 'Age',
                // 'image'    => 'Profile Image',
                'password' => 'Password',
                'email'    => 'Email',
                'category_id'=> 'Category Id',
            ]
        );

        if ($validation->failed()) {
            $errors = $validation->errors();
            return "Validation failed: " . implode(', ', array_map(fn($e) => implode('; ', $e), $errors));
        }

        return "Validation successful!";
    }
    
    public function store(Request $request)
    {
        // $request = Request::createFromGlobals();
        
        $validation = $request->validate([
            'title' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'avatar' => 'required|file|max:2048'
        ]);

        if ($validation->failed()) {
            // Now using fully qualified Response class
            return Response::back()->withErrors($validation);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('profile_images');
        }

        $data = $request->only(['title', 'email']);
        
        return Response::redirect('/success');
    }
}

