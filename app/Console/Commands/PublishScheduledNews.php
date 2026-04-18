<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PublishScheduledNews extends Command
{
    protected $signature = 'news:publish-scheduled';

    protected $description = 'Scheduled haberleri yayına alır';

    public function handle()
    {
        $now = Carbon::now();
        $news = News::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $now)
            ->get();

        foreach ($news as $item) {
            $item->status = 'published';
            $item->save();
        }

        $this->info("Toplam {$news->count()} haber yayınlandı!");
    }
}
