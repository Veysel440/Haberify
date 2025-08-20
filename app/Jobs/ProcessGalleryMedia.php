<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\MediaProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessGalleryMedia implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $articleId,
        public string $disk,
        /** @var string[] */
        public array $originalPaths
    ) {}

    public function handle(MediaProcessor $proc): void
    {
        $article = Article::find($this->articleId);
        if (!$article) return;

        foreach ($this->originalPaths as $i => $path) {
            $dir = "articles/{$this->articleId}/gallery/".($i+1);
            $out = $proc->process($this->disk, $path, $dir, 1600, 900, 400, 225);
        }
    }
}
