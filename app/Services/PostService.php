<?php 
namespace App\Services;
 
class PostService
{
    public function createPost(array $data): Post
    {
        return Post::create($data);
    }

    public function updatePost(int $id, array $data): Post
    {
        $post = Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function deletePost(int $id): void
    {
        Post::destroy($id);
    }
}
