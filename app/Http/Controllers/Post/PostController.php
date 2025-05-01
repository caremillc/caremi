<?php declare(strict_types=1);
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;

class PostController extends Controller
{
    public function index()
    {
        return 'Post index';
    }
    public function create()
    {
        return 'Post create';
    }
    
    public function store(Request $request)
    {
        return 'Post store';
    }

    public function show(int $id)
    {
        return 'Post show '.$id; // Ensure string return
    }

    public function edit(int $id)
    {
        return 'Post edit'.$id;
    }
    public function update(int $id)
    {
        return 'Post update'.$id;
    }
    public function destroy(int $id)
    {
        return 'Post selete'.$id;
    }
}
