<?php

namespace App\Services;


use App\Models\{Article, Page};
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    public function build(): string
    {
        return Cache::remember('sitemap:xml', 600, function () {
            $base = rtrim(config('app.url'),'/');
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            // static
            foreach (['/','/category','/search'] as $p) {
                $xml .= "<url><loc>{$base}{$p}</loc><changefreq>hourly</changefreq><priority>0.8</priority></url>";
            }

            // pages
            Page::where('status','published')->orderByDesc('updated_at')->get()->each(function($p) use (&$xml,$base){
                $loc = "{$base}/page/{$p->slug}";
                $xml .= "<url><loc>{$loc}</loc><lastmod>{$p->updated_at->toAtomString()}</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>";
            });

            // articles
            Article::published()->orderByDesc('published_at')->limit(5000)->get()->each(function($a) use (&$xml,$base){
                $loc = "{$base}/news/{$a->slug}";
                $last = ($a->updated_at ?? $a->published_at ?? now())->toAtomString();
                $xml .= "<url><loc>{$loc}</loc><lastmod>{$last}</lastmod><changefreq>hourly</changefreq><priority>0.9</priority></url>";
            });

            $xml .= '</urlset>';
            return $xml;
        });
    }
}
