<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Queue\SerializesModels;

class CommentSubmitted
{
    use SerializesModels;

    public function __construct(public Article $article, public Comment $comment) {}
}
