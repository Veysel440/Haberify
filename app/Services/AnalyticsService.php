<?php

namespace App\Services;

use App\Models\ArticleView;

class AnalyticsService
{
    public function recordView(int $articleId, string $sessionId, ?string $ip, ?string $ua): void
    {
        ArticleView::create([
            'article_id'=>$articleId,
            'session_id'=>$sessionId,
            'ip'=>$ip,
            'ua'=>$ua,
        ]);
    }
}
