<?php declare(strict_types=1);
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $content = '<h1>Hello World from PostController</h1>';

        return new Response($content);
    }
}
