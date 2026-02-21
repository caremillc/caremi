<?php declare(strict_types=1);

namespace App\Http\Controllers\Post;

use App\Entity\Post;
use App\Http\Controllers\Controller;
use App\Repository\PostMapper;
use App\Repository\PostRepository;
use Careminate\Http\Requests\Request;
use Careminate\Http\Responses\Response;
use Careminate\Supports\Image\ImageManager;

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

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageManager = new ImageManager('images/posts');

            // SEO-friendly filename
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $imageName = $imageManager->upload($_FILES['image'], $slug);

            if ($imageName === null) {
                return new Response("<h1>Image upload failed.</h1>", 400);
            }

            // Resize image to 800x600
            $imageManager->resize($imageName, 800, 600);
            $imagePath = $imageName;
        }

        $post = Post::create(null, $title, $description, $imagePath, null);
        $this->postMapper->save($post);

        // return Response::redirect("/posts");
         return redirect("/posts");
    }

    public function show(int $id): Response
    {
        $post = $this->postRepository->findById($id);
        return view('posts/show.html.twig', compact('post'));
    }

    public function edit(int $id): Response
    {
        $post = $this->postRepository->findById($id);
        return view('posts/edit.html.twig', compact('post'));
    }

    public function update(int $id): Response
    {
        $post = $this->postRepository->findOrFail($id);

        $title       = $this->request->input('title');
        $description = $this->request->input('description');
        $imagePath   = $post->getImage();

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageManager = new ImageManager('images/posts');

            // SEO-friendly filename
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
            $imagePath = $imageManager->replace($_FILES['image'], $post->getImage());
            
            if ($imagePath === null) {
                return new Response("<h1>Image upload failed.</h1>", 400);
            }

            $imageManager->resize($imagePath, 800, 600);
        }

        $post->setTitle($title);
        $post->setDescription($description);
        $post->setImage($imagePath);

        $this->postRepository->update($post);

        // return Response::redirect("/posts");
         return redirect("/posts");
    }

    public function destroy(int $id): Response
    {
        $post = $this->postRepository->findOrFail($id);

        if ($post->getImage()) {
            $imageManager = new ImageManager('images/posts');
            $imageManager->delete($post->getImage());
        }

        $this->postRepository->delete($id);

        // return Response::redirect("/posts");
         return redirect("/posts");
    }
}