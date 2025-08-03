<?php

namespace App\Observers;


use App\Models\CommentLike;
use App\Models\CommentLikeHistory;

class CommentLikeObserver
{
    public function created(CommentLike $like)
    {
        CommentLikeHistory::create([
            'comment_id' => $like->comment_id,
            'user_id'    => $like->user_id,
            'is_like'    => $like->is_like,
            'action'     => $like->is_like ? 'like' : 'dislike'
        ]);
    }

    public function deleted(CommentLike $like)
    {
        CommentLikeHistory::create([
            'comment_id' => $like->comment_id,
            'user_id'    => $like->user_id,
            'is_like'    => $like->is_like,
            'action'     => 'undo'
        ]);
    }

    public function updated(CommentLike $like)
    {
        CommentLikeHistory::create([
            'comment_id' => $like->comment_id,
            'user_id'    => $like->user_id,
            'is_like'    => $like->is_like,
            'action'     => $like->is_like ? 'like' : 'dislike'
        ]);
    }
}
