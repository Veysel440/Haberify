<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\MediaProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessArticleMedia implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $articleId,
        public string $disk,
        public string $originalPath
    ) {}

    public function handle(MediaProcessor $proc): void
    {
        $article = Article::find($this->articleId);
        if (!$article) return;

        $dir = "articles/{$this->articleId}";
        $out = $proc->process($this->disk, $this->originalPath, $dir);


        $article->cover_path = $out['cover'];
        $article->thumb_path = $out['thumb'];
        $article->save();

        // İsteğe bağlı: eski orijinali sil veya arşivle
        // Storage::disk($this->disk)->delete($this->originalPath);
    }
}
