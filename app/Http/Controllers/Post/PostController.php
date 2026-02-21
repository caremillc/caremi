<?php declare (strict_types = 1);
namespace App\Http\Controllers\Post;

use App\Entity\Post;
use App\Http\Controllers\Controller;
use App\Repository\PostMapper;
use App\Repository\PostRepository;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;
use Careminate\Supports\Image\FileUploader;

class PostController extends Controller
{
    public function __construct(
        private PostMapper $postMapper,
        private PostRepository $postRepository
    ) {}

    public function index()
    {
        $request = new Request();
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = 5;
        $offset  = ($page - 1) * $perPage;

        $posts = $this->postRepository->paginate($perPage, $offset);
        $total = $this->postRepository->count();

        return view('posts/index.html.twig', [
            'posts'       => $posts,
            'currentPage' => $page,
            'totalPages'  => ceil($total / $perPage),
        ]);
    }

    public function create(): Response
    {
        // Your logic here
        return view('posts/create.html.twig');
    }

    // public function store(): Response
    // {

    //     $title       = $this->request->input('title');
    //     $description = $this->request->input('description');
    //     $imagePath   = null;

    //     if (empty($title) || empty($description)) {
    //         return new Response("<h1>Error: Title and description are required.</h1>", 400);
    //     }

    //     // Use the helper function to handle file upload
    //     if (isset($_FILES['image'])) {
    //         $imagePath = FileUploader::store($_FILES['image'], storage_path('app/public/images'));

    //         if ($imagePath === null) {
    //             return new Response("<h1>Image upload failed.</h1>", 400);
    //         }
    //     }

    //     // Create the post
    //     $post = Post::create(null, $title, $description, $imagePath, null);

    //     //   dd($post);
    //     $this->postMapper->save($post);

    //     // Debugging output (remove after testing)
    //     // $this->request->getSession()->setFlash('success', sprintf('Post "%s" successfully created', $title)); // step 2
    //     return new Response("/posts");
    // }

    public function store(): Response
    {
        $title       = $this->request->input('title');
        $description = $this->request->input('description');
        $imagePath   = null;

        if (empty($title) || empty($description)) {
            return new Response("<h1>Error: Title and description are required.</h1>", 400);
        }

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            // Create SEO-friendly filename from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            // Preserve original extension
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

            $filename = $slug . '-' . time() . '.' . $extension;

            $imagePath = FileUploader::store(
                $_FILES['image'],
                storage_path('app/public/images'),
                $filename
            );

            if ($imagePath === null) {
                return new Response("<h1>Image upload failed.</h1>", 400);
            }
        }

        $post = Post::create(null, $title, $description, $imagePath, null);
        $this->postMapper->save($post);

        return Response::redirect("/posts");
    }

    public function show(int $id): Response
    {
        $post = $this->postRepository->findById($id);

        return view('posts/show.html.twig', compact('post'));
    }

    public function edit(int $id): Response
    {
        // Your logic here
        $post = $this->postRepository->findById($id);
        return view('posts/edit.html.twig', compact('post'));
    }

    public function update(int $id): Response
    {
        $request = new Request();
        $post    = $this->postRepository->findOrFail($id);

        $title       = $this->request->input('title');
        $description = $this->request->input('description');
        $imagePath   = $post->getImage();

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = FileUploader::store($_FILES['image'], storage_path('app/public/images'));
        }

        $post->setTitle($title);
        $post->setDescription($description);
        $post->setImage($imagePath);

        $this->postRepository->update($post);

        return Response::redirect("/posts");
    }

    public function destroy(int $id): Response
    {
        $this->postRepository->delete($id);
        // flash('success', 'Post deleted successfully');
        return Response::redirect("/posts");
    }
}
