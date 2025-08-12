<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ArticleViewed {
    use SerializesModels;
    public function __construct(public int $articleId, public string $sessionId) {}

}
