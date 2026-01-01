<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Careminate\Exceptions\Http\ValidationException;
use Careminate\Http\Requests\Request;
use Careminate\Http\Requests\UploadedFile;
use Careminate\Http\Responses\ResponseFactory;
use Careminate\Supports\Config;

class UserController
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|min:2|max:50',
                'email' => 'required|email|max:100',
                'password' => 'required|string|min:8',
                'avatar' => 'nullable|image',
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

            // Remove password from response for security
            unset($data['password']);

            return ResponseFactory::json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $data,
            ], 201);

        } catch (ValidationException $e) {
            return ResponseFactory::json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->getErrors(),
            ], 422);
        } catch (\Exception $e) {
            // Use Config::isDebug() method
            return ResponseFactory::json([
                'success' => false,
                'message' => 'Server error',
                'error' => Config::isDebug() ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function apiEndpoint(Request $request)
    {
        // Check for JSON request
        if (!$request->expectsJson()) {
            return ResponseFactory::json([
                'error' => 'Unsupported Media Type',
            ], 415);
        }

        // Parse JSON body
        $data = $request->json();

        if (!$data) {
            return ResponseFactory::json([
                'error' => 'Invalid JSON',
            ], 400);
        }

        // Get specific fields with defaults
        $name = $request->string('name', 'Guest');
        $age = $request->integer('age', 18);
        $active = $request->boolean('active', false);

        return ResponseFactory::json([
            'success' => true,
            'data' => [
                'name' => $name,
                'age' => $age,
                'active' => $active,
                'received_data' => $data,
            ],
        ]);
    }
}