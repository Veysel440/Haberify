<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('login', fn(Request $r) =>
        [Limit::perMinute(5)->by($this->key($r)), Limit::perMinute(20)->by($r->ip())]
        );

        RateLimiter::for('twofactor', fn(Request $r) =>
        [Limit::perMinute(6)->by($this->key($r))]
        );

        RateLimiter::for('comment-create', fn(Request $r) =>
        [Limit::perMinute(3)->by($this->key($r)), Limit::perMinute(10)->by($r->ip())]
        );

        RateLimiter::for('search', fn(Request $r) =>
        [Limit::perMinute(30)->by($this->key($r))]
        );

        RateLimiter::for('media-upload', fn(Request $r) =>
        [Limit::perMinute(6)->by($this->key($r))]
        );

        RateLimiter::for('admin-bulk', fn(Request $r) =>
        [Limit::perMinute(3)->by($this->key($r))]
        );

        RateLimiter::for('password-reset', fn(Request $r) =>
        [Limit::perMinute(5)->by($this->key($r))]
        );

        RateLimiter::for('sessions-list', fn(Request $r) =>
        [Limit::perMinute(20)->by($this->key($r))]
        );
    }

    private function key(Request $r): string
    {
        $uid = optional($r->user())->id ?: 'guest';
        return $uid.'|'.$r->ip();
    }
}
