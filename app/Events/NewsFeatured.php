<?php

namespace App\Events;

use App\Models\News;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsFeatured
{
    use Dispatchable, SerializesModels;

    public News $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
}
