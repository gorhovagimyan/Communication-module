<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Create a new comment.
     */
    public function createComment(array $data): Comment
    {
        return Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $data['post_id'],
            'content' => $data['content'],
            'language' => $data['language'] ?? 'en',
        ]);
    }

    /**
     * Create comment attachments
     *
     * @param $data
     * @param Comment $comment
     * @return Void
     */
    public function createCommentAttachments($data, Comment $comment): Void
    {
        foreach ($data as $file) {
            $path = $file->store('attachments', 'public');
            $comment->attachments()->create([
                'file_path' => $path,
                'type' => $file->getClientMimeType(),
            ]);
        }
    }

    /**
     * Update an existing comment.
     */
    public function updateComment(Comment $comment, array $data): Comment
    {
        $comment->update([
            'post_id' => $data['post_id'],
            'content' => $data['content'],
            'language' => $data['language'] ?? $comment->language,
        ]);

        return $comment;
    }
}
