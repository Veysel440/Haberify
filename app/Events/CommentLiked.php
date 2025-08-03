<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentLiked
{
    use Dispatchable, SerializesModels;

    public int $commentId;
    public int $userId;
    public bool $isLike;

    public function __construct(int $commentId, int $userId, bool $isLike)
    {
        $this->commentId = $commentId;
        $this->userId = $userId;
        $this->isLike = $isLike;
    }
}
