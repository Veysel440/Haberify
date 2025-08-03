<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentLikedNotification extends Notification
{
    use Queueable;

    public int $commentId;
    public int $likedBy;
    public bool $isLike;

    public function __construct(int $commentId, int $likedBy, bool $isLike)
    {
        $this->commentId = $commentId;
        $this->likedBy = $likedBy;
        $this->isLike = $isLike;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'comment_id' => $this->commentId,
            'liked_by'   => $this->likedBy,
            'type'       => $this->isLike ? 'like' : 'dislike',
            'message'    => $this->isLike
                ? 'Yorumunuza bir kullanıcı beğeni verdi.'
                : 'Yorumunuza bir kullanıcı dislike verdi.',
        ];
    }
}
