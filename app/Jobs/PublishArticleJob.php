<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Events\ArticlePublished;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PublishArticleJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $articleId) {}

    public function handle(): void
    {
        /** @var Article|null $a */
        $a = Article::find($this->articleId);
        if (!$a || $a->status !== 'scheduled') return;

        $a->update(['status'=>'published','published_at'=>now(),'scheduled_at'=>null]);
        $a->searchable();
        cache()->forget('rss:latest'); cache()->forget('sitemap:xml');
        event(new ArticlePublished($a));
    }

    public function failed(\Throwable $e): void
    {
        Log::error('job.publish_article.failed', ['id'=>$this->articleId,'err'=>$e->getMessage()]);
    }
}
