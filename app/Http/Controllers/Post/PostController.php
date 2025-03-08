<?php 
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;

class PostController extends Controller 
{
    public function index()
    {
        echo 'welcome to home index page';
    }

    public function create()
    {
        echo 'welcome to post create page'; 
    }
    public function store()
    {
        echo 'welcome to post store page'; 
    }
    public function edit(int $id)
    {
        echo 'welcome to post edit page id '.$id; 
    }
    public function update(int $id)
    {
        echo 'welcome to post update page id '.$id; 
    }
    public function show(int $id)
    {
        echo 'welcome to post show page '.$id; 
    }
    public function destroy(int $id)
    {
        echo 'welcome to post delete page id'.$id; 
    }
}
