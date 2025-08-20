<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PublishScheduledArticles implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $due = Article::query()
            ->where('status','scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at','<=', now())
            ->limit(200)
            ->get();

        foreach ($due as $a) {
            $a->status = 'published';
            $a->published_at = now();
            $a->scheduled_at = null;
            $a->save();

            event(new \App\Events\ArticlePublished($a->id));
        }
    }
}
