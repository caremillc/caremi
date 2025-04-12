<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        return 'display all posts';
    }
    public function create()
    {
        return 'create new post';
    }
    public function store()
    {
        return 'store new post';
    }
    public function show(int $id)
    {
        return 'show post with id = ' . $id;
    }
    public function edit(int $id)
    {
        return 'edit post with id = ' . $id;
    }
    public function update(int $id)
    {
        return 'update post with id = ' . $id;
    }
    public function destroy(int $id)
    {
        return 'delete post with id = ' . $id;
    }
}
