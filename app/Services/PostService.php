<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostService
{
    /**
     * Create a new post.
     */
    public function createPost(array $data): Post
    {
        return Post::create([
            'user_id' => Auth::id(),
            'content' => $data['content'],
            'language' => $data['language'] ?? 'en',
        ]);
    }

    /**
     * Create post attachments.
     */
    public function createPostAttachments($data, Post $post): Void
    {
        foreach ($data as $file) {
            $path = $file->store('attachments', 'public');
            $post->attachments()->create([
                'file_path' => $path,
                'type' => $file->getClientMimeType(),
            ]);
        }
    }

    /**
     * Update an existing post.
     */
    public function updatePost(Post $post, array $data): Post
    {
        $post->update([
            'content' => $data['content'],
            'language' => $data['language'] ?? $post->language,
        ]);

        return $post;
    }

}
