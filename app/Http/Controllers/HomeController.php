<?php

namespace App\Http\Controllers;

use Careminate\Http\Validations\Validate;

class HomeController extends Controller
{
    public function index()
    {
        $requests = [
            'name'                  => $_POST['name'] ?? 'eric',
            'age'                   => $_POST['age'] ?? 20,
           'image'                  => $_FILES['image'] ?? null,
            'password'              => $_POST['password'] ?? 'password',
            'password_confirmation' => $_POST['password_confirmation'] ?? 'password',
            'email'                 => $_POST['email'] ?? 'eric@gmail.com',
            'category_id'           => $_POST['name'] ?? 1,
        ];

        $validation = Validate::make($requests,[
                'name'     => 'required|string|min:3|unique:users',
                'age'      => 'required|integer|min:18',
                'image'    => 'required|file',
                'password' => 'required|confirmed',
                'email' => 'required|email|unique:users,email',
                'category_id' => 'required|exists:categories,id'
            ],
            [
                'name'     => 'Full Name',
                'age'      => 'Age',
                'image'    => 'Profile Image',
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
}
