<?php

namespace App\Http\Middleware;

use Closure;

class CacheHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        $resp = $next($request);

        if ($request->user() || $request->headers->has('Authorization')) {
            $resp->headers->set('Cache-Control','no-store, private');
            return $resp;
        }


        if ($request->routeIs('articles.show')) {

            $resp->headers->set('Cache-Control','public, max-age=300, s-maxage=600, stale-while-revalidate=120');
        } elseif ($request->routeIs('articles.index','tags.index','categories.index','search')) {
            $resp->headers->set('Cache-Control','public, max-age=60, s-maxage=120, stale-while-revalidate=60');
        } elseif ($request->routeIs('menus.show','pages.show')) {
            $resp->headers->set('Cache-Control','public, max-age=180, s-maxage=300');
        } else {
            $resp->headers->set('Cache-Control','public, max-age=60');
        }

        return $resp;
    }
}
