<?php declare(strict_types=1);
namespace App\Http\Controllers;

use Careminate\Http\Responses\Response;

class HomeController extends Controller 
{
    public function index(): Response
    {
        return Response::success('User created', ['id' => 123]);
        return Response::error('Validation failed', ['email' => 'Email is required']);
        return Response::fromThrowable($exception);
        return response()->json(['custom' => true]); // using helper
    }
}