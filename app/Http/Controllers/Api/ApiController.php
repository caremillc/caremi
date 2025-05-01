<?php declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;

class ApiController extends Controller
{
    public function getPosts(Request $request)
    {
        Response::json(['data' => $request], Response::HTTP_CREATED)->send();
    }
}