<?php declare (strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Http\Requests\UploadedFile;
use Careminate\Http\Responses\JsonResponse;
use Careminate\Http\Responses\ViewResponse;
use Careminate\Exceptions\ValidationException;
use Careminate\Http\Requests\RequestValidator;
use Careminate\Http\Responses\RedirectResponse;

    class UserController extends Controller
{
    public function index(): ViewResponse
    {
        return new ViewResponse('home', ['title' => 'Welcome to Careminate']);
    }

    public function api(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok', 'time' => time()]);
    }

    public function go(): RedirectResponse
    {
        return new RedirectResponse('/api');
    }
    
    public function profile(): JsonResponse
    {
        $user = ['id' => 1, 'name' => 'Tony Stark', 'role' => 'Genius, Billionaire, Philanthropist'];
        return new JsonResponse($user);
    }

    public function logout(): RedirectResponse
    {
        // Clear session, cookies, etc.
        return new RedirectResponse('/login');
    }
    
    public function store(Request $request)
    {
        // Validate input
        $validator = RequestValidator::make($request);
        
        try {
            $data = $validator->validate([
                'name' => 'required|string|min:2|max:50',
                'email' => 'required|email|max:100',
                'password' => 'required|string|min:8',
                'avatar' => 'nullable|image|max:2048', // 2MB
                'terms' => 'required|boolean',
            ]);
            
            // Process file upload
            if ($request->has('avatar') && $request->files['avatar'] instanceof UploadedFile) {
                $avatar = $request->files['avatar'];
                
                // Validate file
                $avatar->validate([
                    'maxSize' => 2 * 1024 * 1024,
                    'allowedMimeTypes' => ['image/jpeg', 'image/png'],
                ]);
                
                // Store file
                $path = $avatar->store('uploads/avatars');
                $data['avatar_path'] = $path;
            }
            
            // Create user
            // $user = User::create($data);
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $data,
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->getErrors(),
            ], 422);
        }
    }
    
    public function apiEndpoint(Request $request)
    {
        // Check for JSON request
        if (!$request->expectsJson()) {
            return response()->json([
                'error' => 'Unsupported Media Type'
            ], 415);
        }
        
        // Parse JSON body
        $data = $request->json();
        
        if (!$data) {
            return response()->json([
                'error' => 'Invalid JSON'
            ], 400);
        }
        
        // Get specific fields
        $name = $request->string('name');
        $age = $request->integer('age', 18);
        $active = $request->boolean('active', false);
        
        // Process request
        // ...
        
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $name,
                'age' => $age,
                'active' => $active,
            ]
        ]);
    }
}

   

   
