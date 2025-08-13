<?php

namespace App\Providers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
class RouteServiceProvider
{


    public function boot(): void
    {
        parent::boot();

        RateLimiter::for('login', fn($r)=> Limit::perMinute(5)->by(strtolower($r->input('email','')).'|'.$r->ip()));
        RateLimiter::for('twofactor', fn($r)=> Limit::perMinute(5)->by(($r->input('tmp','')).'|'.$r->ip()));
        RateLimiter::for('comment-create', fn($r)=> Limit::perMinute(10)->by(($r->user()?->id ?? $r->ip())));
        RateLimiter::for('media-upload', fn($r)=> Limit::perMinute(12)->by(($r->user()?->id ?? $r->ip())));
        RateLimiter::for('search', fn($r)=> Limit::perMinute(30)->by($r->ip()));
        RateLimiter::for('admin-bulk', fn($r)=> Limit::perMinute(6)->by(($r->user()?->id ?? $r->ip())));

        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }

}
