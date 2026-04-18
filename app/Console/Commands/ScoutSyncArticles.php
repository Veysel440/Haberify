<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class ScoutSyncArticles extends Command
{
    protected $signature = 'scout:articles:sync';

    protected $description = 'Import published articles to Meilisearch';

    public function handle(): int
    {
        Article::published()->with(['category', 'tags'])->orderBy('id')->chunkById(500, function ($chunk) {
            $chunk->searchable();
        });
        $this->info('Indexed.');

        return self::SUCCESS;
    }
}
