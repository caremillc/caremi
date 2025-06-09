<?php
namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;

class PostController extends Controller
{
    public function index()
    {
        echo 'all posts';
    }

    public function create()
    {
        echo 'create new post';
    }

   public function store(Request $request)
    {
        // Simulate file upload
        if (request()->file('avatar')) {
            $stored = request()->file('avatar')->store('uploads');
            echo "Stored at: " . $stored;
        }

        echo "Input: " . request('name');
    }

    public function edit(int $id)
    {
        echo 'edit post ' . $id;
    }
    public function update(Request $request ,int $id)
    {
        echo 'update post ' . $id;
    }
    public function show(int $id)
    {
        echo 'show post ' . $id;
    }

    public function destroy(int $id)
    {
        echo 'delete post ' . $id;
    }
}
