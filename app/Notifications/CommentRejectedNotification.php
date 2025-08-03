<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentRejectedNotification extends Notification
{
    use Queueable;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Yorumunuz reddedildi.',
            'comment_id' => $this->comment->id,
            'news_id' => $this->comment->news_id,
        ];
    }
}
