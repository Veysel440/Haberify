<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;

class ScoutSyncArticles extends Command
{
    protected $signature = 'scout:articles:sync';
    protected $description = 'Import published articles to Meilisearch';

    public function handle(): int
    {
        Article::published()->with(['category','tags'])->orderBy('id')->chunkById(500, function($chunk){
            $chunk->searchable();
        });
        $this->info('Indexed.');
        return self::SUCCESS;
    }
}
