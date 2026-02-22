<?php declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Entity\User;
use App\Repository\UserMapper;
use Careminate\Http\Responses\Response;

class UserController extends Controller
{
    public function __construct(
        private UserMapper $userMapper
    ) {}

    public function index(): Response
    {
        $users = $this->userMapper->findAll(); // Replace later with repository fetch
        return view('users/index.html.twig', compact('users'));
    }

    public function create(): Response
    {
        return view('users/create.html.twig');
    }

    public function store(): Response
    {
        $data = $this->request->all();

        // 🔎 Basic Validation
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['role'])
        ) {
            return new Response('<h1>Validation failed: All fields are required</h1>', 422);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return new Response('<h1>Invalid email address</h1>', 422);
        }

        try {
            // 🧱 Create Entity
            $user = User::create(
                $data['name'],
                $data['email'],
                $data['role'],
                $data['password']
            );

            // 💾 Persist
            $this->userMapper->save($user);

        } catch (\Exception $e) {
            return new Response(
                '<h1>Error: ' . htmlspecialchars($e->getMessage()) . '</h1>',
                400
            );
        }

        // ✅ Redirect after success
        return redirect('/users');
    }

    public function show(int $id): Response
    {
        $user = $this->userMapper->findById($id);
        if (!$user) {
            return new Response('<h1>User not found</h1>', 404);
        }
        return view('users/show.html.twig', compact('user'));
    }

    public function edit(int $id): Response
    {
        $user = $this->userMapper->findById($id);
        if (!$user) {
            return new Response('<h1>User not found</h1>', 404);
        }
        return view('users/edit.html.twig', compact('user'));
    }

    public function update(int $id): Response
    {
        return new Response("<h1>Update User with ID: $id</h1>");
    }

    public function delete(int $id): Response
    {
        return new Response("<h1>Delete User with ID: $id</h1>");
    }
}