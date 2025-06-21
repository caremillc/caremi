<?php declare(strict_types=1);

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Careminate\Http\Requests\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        echo   route('posts.show', ['id' => 42]); // → /posts/42/edit
        exit;
        $posts = Post::all(); // or paginate if implemented
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Post::create($validated);

        return redirect('/posts'); // simple redirect
    }

    public function show($id)
    {
        echo $id;
        exit;
        $post = Post::findOrFail($id); // Careminate must support or handle fallback
        return view('posts.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validated);

        return redirect('/posts');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect('/posts');
    }
}
