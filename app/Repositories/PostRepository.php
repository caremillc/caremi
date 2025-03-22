<?php 
namespace App\Repositories;

use App\Support\Pagination\Paginator;

class PostRepository
{
    public function paginate(int $perPage): Paginator
    {
        return Post::paginate($perPage);
    }

    public function findOrFail(int $id): Post
    {
        return Post::findOrFail($id);
    }
}
