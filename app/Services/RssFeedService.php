<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class RssFeedService
{
    public function build(): string
    {
        return Cache::remember('rss:latest', 300, function () {
            $items = Article::published()
                ->with('author:id,name','category:id,slug,name')
                ->orderByDesc('published_at')->limit(50)->get();

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<rss version="2.0"><channel>';
            $xml .= '<title>Haberify</title><link>'.e(config('app.url')).'</link><description>Haberify RSS</description>';
            foreach ($items as $a) {
                $link = url('/news/'.$a->slug);
                $xml .= '<item>';
                $xml .= '<title>'.e($a->title).'</title>';
                $xml .= '<link>'.e($link).'</link>';
                $xml .= '<guid isPermaLink="true">'.e($link).'</guid>';
                $xml .= '<pubDate>'.$a->published_at?->toRfc2822String().'</pubDate>';
                $xml .= '<category>'.e($a->category?->name ?? 'Genel').'</category>';
                $xml .= '<description><![CDATA['.($a->summary ?? str(strip_tags($a->body))->limit(220)).']]></description>';
                $xml .= '</item>';
            }
            $xml .= '</channel></rss>';
            return $xml;
        });
    }
}
