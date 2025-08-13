<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\VisitService;
use Closure;
use Illuminate\Support\Str;

class TrackReferrer
{
    public function __construct(private VisitService $svc) {}

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->method() !== 'GET') return $response;

        if ($request->routeIs('articles.show')) {
            $sessionId = $request->session()->get('sid') ?? tap(Str::uuid()->toString(), fn($v)=>$request->session()->put('sid',$v));
            $referer = $request->headers->get('Referer');
            $ref = ref_host($referer);

            $this->svc->record([
                'session_id'  => $sessionId,
                'path'        => $request->path(),
                'ref'         => $ref,
                'utm_source'  => $request->query('utm_source'),
                'utm_medium'  => $request->query('utm_medium'),
                'utm_campaign'=> $request->query('utm_campaign'),
                'ip'          => $request->ip(),
                'ua'          => Str::limit((string)$request->userAgent(), 255, ''),
                'article_id'  => (int) $request->attributes->get('article_id', 0) ?: null,
            ]);
        }

        return $response;
    }
}
