<?php declare(strict_types=1);
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Careminate\Http\Responses\Response;

class UserController extends Controller
{
    public function index(): Response
    { 
        $users = "All users";

       return view('users/index.html.twig', compact('users'));
    }

    public function create(): Response
    {
        // Your logic here
        return view('users/create.html.twig');
    }

   public function store(): Response
    {
        // Your logic here
        var_dump($this->request->all());
       
        return new Response("<h1>User store successfully</h1>");
    }

    public function show(int $id): Response
    {
        // Your logic here
        $postId = "<h1>Show Post with ID: $id</h1>";
        return view('users/show.html.twig', compact('postId'));
    }

    public function edit(int $id): Response
    {
        // Your logic here
        $postId = "<h1>Edit Post with ID: $id</h1>";
        return view('users/edit.html.twig', compact('postId'));
    }

    public function update(int $id): Response
    {
        // Your logic here
        return new Response("<h1>Update Post with ID: $id</h1>");
    }

    public function delete(int $id): Response
    {
        // Your logic here
        return new Response("<h1>Delete Post with ID: $id</h1>");
    }
}
 
