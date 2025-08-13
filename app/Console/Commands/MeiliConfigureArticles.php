<?php
declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;

class MeiliConfigureArticles extends Command
{
    protected $signature = 'meili:articles:configure';
    protected $description = 'Articles index ayarlarını yapar';

    public function handle(): int
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('articles');

        $index->updateSettings([
            'searchableAttributes' => ['title','summary','body','tags','category'],
            'filterableAttributes' => ['language','category','published_at'],
            'sortableAttributes'   => ['published_at'],
            'rankingRules' => [
                'words',
                'typo',
                'proximity',
                'attribute',
                'sort',
                'exactness',
            ],
            'stopWords' => ['ve','ile','da','de','mi','mı','mu','mü','bir','bu'],
            'synonyms' => [
                'haber' => ['makale','yazı'],
                'son dakika' => ['acil','flash'],
            ],
            'typoTolerance' => ['enabled'=>true, 'minWordSizeForTypos'=>['oneTypo'=>5,'twoTypos'=>9]],
        ]);

        $this->info('Meilisearch articles index ayarlandı.');
        return self::SUCCESS;
    }
}
