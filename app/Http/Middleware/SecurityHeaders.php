<?php

namespace App\Http\Middleware;

use Closure;

class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $resp = $next($request);
        $resp->headers->set('X-Content-Type-Options', 'nosniff');
        $resp->headers->set('X-Frame-Options', 'DENY');
        $resp->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $resp->headers->set('X-XSS-Protection', '0');
        $resp->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        return $resp;
    }
}
