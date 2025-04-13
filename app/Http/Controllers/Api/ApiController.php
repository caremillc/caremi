<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Careminate\Http\Responses\ApiResponse;


class ApiController extends Controller
{
    public function index()
    {
        return ApiResponse::json([
            'message' => 'Welcome to the API',
            'status'  => 'success'
        ]);

        
    }

    public function show($id)
    {
        // Sample API action with dynamic parameter
        return ApiResponse::json([
            'message' => 'User data',
            'user'    => ['id' => $id, 'name' => 'John Doe']
        ]);
    }
}