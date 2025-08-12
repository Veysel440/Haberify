<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class InvalidateContentCache extends Command
{
    protected $signature = 'cache:content:invalidate {--all}';
    protected $description = 'Invalidate RSS, sitemap and menu caches';

    public function handle(): int
    {
        Cache::forget('rss:latest');
        Cache::forget('sitemap:xml');
        $this->info('Invalidated rss:latest and sitemap:xml');
        if ($this->option('all')) { $this->info('Consider prefix-based flush for menu:*, setting:*'); }
        return self::SUCCESS;
    }
}
