<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class ETagMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->method() !== 'GET') return $next($request);

        $response = $next($request);
        if (!$response->headers->has('ETag')) {
            $etag = '"' . sha1($response->getContent()) . '"';
            $response->headers->set('ETag', $etag);
            if ($request->headers->get('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }
        $response->headers->set('Cache-Control','public, max-age=60');
        return $response;
    }
}
