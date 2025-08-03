<?php

namespace App\Services\Comment;


use App\Models\CommentLike;
use App\Models\Comment;
use App\Events\CommentLiked;
use App\Notifications\CommentLikedNotification;
use Illuminate\Support\Facades\DB;

class CommentLikeService
{
    public function like(int $commentId, int $userId, bool $isLike): void
    {
        DB::transaction(function () use ($commentId, $userId, $isLike) {

            $like = CommentLike::withTrashed()
                ->where('comment_id', $commentId)
                ->where('user_id', $userId)
                ->first();

            if ($like && $like->trashed()) {

                $like->restore();
                $like->is_like = $isLike;
                $like->save();
            } else {
                CommentLike::updateOrCreate(
                    ['comment_id' => $commentId, 'user_id' => $userId],
                    ['is_like' => $isLike]
                );
            }

            event(new CommentLiked($commentId, $userId, $isLike));

            $comment = Comment::find($commentId);
            if ($comment && $comment->user_id !== $userId) {
                $comment->user->notify(new CommentLikedNotification($commentId, $userId, $isLike));
            }
        });
    }

    public function getLikeStats(int $commentId): array
    {
        $likes = CommentLike::where('comment_id', $commentId)->where('is_like', true)->count();
        $dislikes = CommentLike::where('comment_id', $commentId)->where('is_like', false)->count();
        return [
            'likes' => $likes,
            'dislikes' => $dislikes,
        ];
    }

    public function undo(int $commentId, int $userId): void
    {
        $like = CommentLike::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();
        if ($like) {
            $like->delete();
        }
    }
}
