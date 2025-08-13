<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\PublishArticleJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled {--limit=200}';
    protected $description = 'scheduled_at <= now olan makaleleri publish kuyruğuna atar';

    public function handle(): int
    {
        $lock = Cache::lock('articles:publish-scheduled', 55);
        if (!$lock->get()) { $this->info('Lock aktif, atlanıyor.'); return self::SUCCESS; }

        try {
            $limit = (int) $this->option('limit');
            $ids = DB::table('articles')
                ->where('status','scheduled')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at','<=',now())
                ->orderBy('scheduled_at')
                ->limit($limit)
                ->pluck('id');

            foreach ($ids as $id) {
                PublishArticleJob::dispatch((int)$id);
            }
            $this->info('Queued: '.count($ids));
        } finally { $lock->release(); }

        return self::SUCCESS;
    }
}
