<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsService;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TrackArticleView
{
    public function __construct(private AnalyticsService $analytics) {}

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('api/v1/articles/*') && $request->method() === 'GET') {
            $article = $request->route('slug');

            $id = (int) $request->attributes->get('article_id', 0);
            if ($id > 0) {
                $sid = $request->session()->get('sid') ?? tap(Str::uuid()->toString(), fn($v)=>$request->session()->put('sid',$v));
                $key = "viewed:{$id}:{$sid}";
                if (Cache::add($key, 1, now()->addMinutes(30))) {
                    $this->analytics->recordView($id, $sid, $request->ip(), $request->userAgent());
                }
            }
        }
        return $response;
    }
}
