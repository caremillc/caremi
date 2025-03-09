<?php 
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        return view('posts.index');
    }
    public function create()
    {
        return view('posts.create');
    }
    public function store()
    {
        return redirect('posts');
    }
    public function show($id)
    {
        return redirect('posts.show');
    }
    public function edit($id)
    {
        return redirect('posts.edit');
    }
    public function update($id)
    {
        return redirect('posts');
    }
    public function destroy($id)
    {
        return redirect('posts');
    }
}
