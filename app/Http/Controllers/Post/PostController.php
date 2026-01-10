<?php declare(strict_types=1);

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Http\Requests\UploadedFile;
use Careminate\Http\Responses\JsonResponse;
use Careminate\Http\Responses\ViewResponse;
use Careminate\Http\Requests\RequestValidator;
use Careminate\Http\Responses\RedirectResponse;
use Careminate\Exceptions\Http\ValidationException;

class PostController extends Controller
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
        return new RedirectResponse('/login');
    }
    
    public function store(Request $request): JsonResponse
    {
        $validator = RequestValidator::make($request);
        
        try {
            $data = $validator->validate([
                'name' => 'required|string|min:2|max:50',
                'email' => 'required|email|max:100',
                'password' => 'required|string|min:8',
                'avatar' => 'nullable|image|max:2048',
                'terms' => 'required|boolean',
            ]);
            
            // Process file upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                
                if ($avatar instanceof UploadedFile) {
                    $avatar->validate([
                        'maxSize' => 2 * 1024 * 1024,
                        'allowedMimeTypes' => ['image/jpeg', 'image/png'],
                    ]);
                    
                    $path = $avatar->store('uploads/avatars');
                    $data['avatar_path'] = $path;
                }
            }
            
            // Return JsonResponse directly
            return new JsonResponse([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $data,
            ], 201);
            
        } catch (ValidationException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->getErrors(),
            ], 422);
        }
    }
    
    public function apiEndpoint(Request $request): JsonResponse
    {
        // Check for JSON request
        if (!$request->expectsJson()) {
            return new JsonResponse([
                'error' => 'Unsupported Media Type'
            ], 415);
        }
        
        // Parse JSON body
        $data = $request->json();
        
        if (!$data) {
            return new JsonResponse([
                'error' => 'Invalid JSON'
            ], 400);
        }
        
        // Get specific fields
        $name = $request->string('name');
        $age = $request->integer('age', 18);
        $active = $request->boolean('active', false);
        
        return new JsonResponse([
            'success' => true,
            'data' => [
                'name' => $name,
                'age' => $age,
                'active' => $active,
            ]
        ]);
    }
}