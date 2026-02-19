<?php declare(strict_types=1);
namespace App\Http\Controllers\Post;

use App\Entity\Post;
use App\Http\Controllers\Controller;
use Careminate\Supports\Image\FileUploader;
use Careminate\Http\Responses\Response;


class PostController extends Controller
{
    public function index(): Response
    { 
        $posts = "All Posts";

       return view('posts/index.html.twig', compact('posts'));
    }

    public function create(): Response
    {
        // Your logic here
        return view('posts/create.html.twig');
    }

    public function store(): Response
    {
       
        $title       = $this->request->input('title');
        $description = $this->request->input('description');
        $imagePath   = null;

        if (empty($title) || empty($description)) {
            return new Response("<h1>Error: Title and description are required.</h1>", 400);
        }

        // Use the helper function to handle file upload
        if (isset($_FILES['image'])) {
            $imagePath = FileUploader::store($_FILES['image'], storage_path('app/public/images'));

            if ($imagePath === null) {
                return new Response("<h1>Image upload failed.</h1>", 400);
            }
        }

        // Create the post
        $post = Post::create(null, $title, $description, $imagePath, null);

        dd($post);
        //$this->postMapper->save($post);
        // Debugging output (remove after testing)
       // $this->request->getSession()->setFlash('success', sprintf('Post "%s" successfully created', $title)); // step 2
         return new Response("/posts");
    }

    public function show(int $id): Response
    {
        // Your logic here
        $postId = "<h1>Show Post with ID: $id</h1>";
        return view('posts/show.html.twig', compact('postId'));
    }

    public function edit(int $id): Response
    {
        // Your logic here
        $postId = "<h1>Edit Post with ID: $id</h1>";
        return view('posts/edit.html.twig', compact('postId'));
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
