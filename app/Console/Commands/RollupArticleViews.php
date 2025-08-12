<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RollupArticleViews extends Command
{
    protected $signature = 'analytics:rollup {--from=} {--to=}';
    protected $description = 'Aggregate article_views into daily table';

    public function handle(): int
    {
        $from = $this->option('from') ?: now()->subDay()->startOfDay()->toDateString();
        $to   = $this->option('to')   ?: now()->endOfDay()->toDateString();

        /** @lang MySQL */
        DB::statement("
            INSERT INTO article_view_daily (day, article_id, views)
            SELECT DATE(created_at) as day, article_id, COUNT(*)
            FROM article_views
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at), article_id
            ON DUPLICATE KEY UPDATE views = VALUES(views)
        ", [$from, $to]);

        $this->info("Rolled up from {$from} to {$to}");
        return self::SUCCESS;
    }
}
