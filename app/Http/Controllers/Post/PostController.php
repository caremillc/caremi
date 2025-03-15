<?php 
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

class PostController extends Controller 
{
    public function index()
    {
        return 'Welcome to the post index page';
    }

    public function create()
    {
        return 'Welcome to the post create page'; 
    }

    public function store()
    {
        return 'Welcome to the post store page'; 
    }

    public function edit(int $id)
    {
        return 'Welcome to the post edit page with ID ' . $id; 
    }

    public function update(int $id)
    {
        return 'Welcome to the post update page with ID ' . $id; 
    }

    public function show(int $id)
    {
        return 'Welcome to the post show page with ID ' . $id; 
    }

    public function destroy(int $id)
    {
        return 'Welcome to the post delete page with ID ' . $id; 
    }
}
