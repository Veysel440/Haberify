<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Queue\SerializesModels;

class CommentModerated
{
    use SerializesModels;

    public function __construct(public Comment $comment, public string $status) {}
}
