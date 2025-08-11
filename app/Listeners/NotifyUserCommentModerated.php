<?php

namespace App\Listeners;

use App\Events\CommentModerated;
use App\Events\NotificationCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyUserCommentModerated implements ShouldQueue
{
    public function handle(CommentModerated $event): void
    {
        $userId = $event->comment->user_id;
        if (!$userId) return;

        event(new NotificationCreated($userId, [
            'type' => 'comment_moderated',
            'comment_id' => $event->comment->id,
            'status' => $event->status,
        ]));
    }
}
