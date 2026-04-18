<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-XSS-Protection', '0');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Content-Security-Policy', $this->buildContentSecurityPolicy());

        return $response;
    }

    private function buildContentSecurityPolicy(): string
    {
        $extraConnect = $this->envList('CSP_CONNECT_SRC');
        $extraImg = $this->envList('CSP_IMG_SRC');

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline'",
            'img-src ' . trim("'self' data: https: " . implode(' ', $extraImg)),
            "font-src 'self' data:",
            'connect-src ' . trim("'self' " . implode(' ', $extraConnect)),
        ];

        return implode('; ', $directives);
    }

    /**
     * @return array<int, string>
     */
    private function envList(string $key): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) env($key, '')))));
    }
}
