<?php declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

class ApiController extends Controller
{
    public function getPosts(Request $request)
    {
        return Response::json([
            'data' => $request->all(),
            'status' => 'success'
        ]);
    }


    public function login(Request $request)
    {
        // Your login logic
        return Response::json([
            'token' => 'generated_token_here',
            'user' => [
                'id' => 1,
                'name' => 'Test User'
            ]
        ]);
    }
}