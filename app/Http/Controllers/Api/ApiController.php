<?php 
namespace App\Http\Controllers\Api;

use Careminate\Http\Responses\ApiResponse;

class ApiController
{
    public function index()
    {
        // Sample API action
        return ApiResponse::json([
            'message' => 'Welcome to the API',
            'status'  => 'success'
        ]);
    }

    public function showUser($id)
    {
        // Sample API action with dynamic parameter
        return ApiResponse::json([
            'message' => 'User data',
            'user'    => ['id' => $id, 'name' => 'John Doe']
        ]);
    }
}
