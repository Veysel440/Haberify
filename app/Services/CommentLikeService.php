<?php

namespace App\Services;

use App\Models\CommentLike;

class CommentLikeService implements CommentLikeServiceInterface
{
    public function like(int $commentId, int $userId, bool $isLike): void
    {
        CommentLike::updateOrCreate(
            ['comment_id' => $commentId, 'user_id' => $userId],
            ['is_like' => $isLike]
        );
    }

    public function getLikeStats(int $commentId): array
    {
        $likes = CommentLike::where('comment_id', $commentId)->where('is_like', true)->count();
        $dislikes = CommentLike::where('comment_id', $commentId)->where('is_like', false)->count();
        return ['likes' => $likes, 'dislikes' => $dislikes];
    }
}
