<?php

namespace App\Services;

use App\Models\Reaction;
use Illuminate\Support\Facades\Auth;

class ReactionService
{
    /**
     * Create a new reaction.
     */
    public function createReaction(array $data): Reaction
    {
        return Reaction::create([
            'user_id' => Auth::id(),
            'post_id' => $data['post_id'] ?? null,
            'comment_id' => $data['comment_id'] ?? null,
            'type' => $data['type'],
        ]);
    }

    /**
     * Update an existing reaction.
     */
    public function updateReaction(Reaction $reaction, array $data): Reaction
    {
        $reaction->update([
            'type' => $data['type'],
        ]);

        return $reaction;
    }
}
