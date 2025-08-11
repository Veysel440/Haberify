<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ThrottlePerRole
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle($request, Closure $next, string $key='api', int $max=60, int $decay=60)
    {
        $role = $request->user()?->getRoleNames()->first() ?: 'guest';
        $signature = sprintf('throttle:%s:%s:%s', $key, $role, $request->ip());

        if ($this->limiter->tooManyAttempts($signature, $this->limits($role,$max))) {
            throw new ThrottleRequestsException('Too Many Requests.');
        }

        $this->limiter->hit($signature, $this->decay($role,$decay));
        $response = $next($request);
        $remaining = $this->limits($role,$max) - $this->limiter->attempts($signature);

        return tap($response, function ($resp) use ($remaining, $decay) {
            $resp->headers->set('X-RateLimit-Remaining', max($remaining,0));
            $resp->headers->set('X-RateLimit-Reset', $decay);
        });
    }

    private function limits(string $role, int $default): int
    {
        return match($role){
            'admin'  => 600,
            'editor' => 300,
            'author' => 200,
            default  => $default,
        };
    }

    private function decay(string $role, int $default): int
    {
        return match($role){
            'admin'  => 60,
            'editor' => 60,
            'author' => 60,
            default  => $default,
        };
    }
}
