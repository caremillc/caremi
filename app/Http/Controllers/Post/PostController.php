<?php 
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

class PostController extends Controller 
{
    public function index()
    {
        return 'welcome to post index page';
    }

    public function create()
    {
        return 'welcome to post create page'; 
    }
    public function store()
    {
        return 'welcome to post store page'; 
    }
    public function edit(int $id)
    {
        return 'welcome to post edit page id '.$id; 
    }
    public function update(int $id)
    {
        return 'welcome to post update page id '.$id; 
    }
    public function show(int $id)
    {
        return 'welcome to post show page '.$id; 
    }
    public function destroy(int $id)
    {
        return 'welcome to post delete page id'.$id; 
    }
}
