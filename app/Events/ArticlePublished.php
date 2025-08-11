<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Queue\SerializesModels;

class ArticlePublished
{
    use SerializesModels;

    public function __construct(public Article $article) {}
}
