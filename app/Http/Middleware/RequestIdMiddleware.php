<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class RequestIdMiddleware
{
    public function handle($request, Closure $next)
    {
        $rid = $request->headers->get('X-Request-Id') ?: Str::uuid()->toString();
        app('log')->withContext(['request_id' => $rid]);
        $response = $next($request);
        $response->headers->set('X-Request-Id', $rid);
        return $response;
    }
}
