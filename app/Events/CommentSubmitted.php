<?php

namespace App\Events;

use App\Models\{Article, Comment};
use Illuminate\Queue\SerializesModels;

class CommentSubmitted
{
    use SerializesModels;

    public function __construct(public Article $article, public Comment $comment) {}
}
