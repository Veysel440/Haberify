<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendWeeklyDigest implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(): void
    {
        $subs = \App\Models\User::query()->whereNotNull('email_verified_at')->get(['id','email','name']);
        $top = \App\Models\Article::query()
            ->where('status','published')
            ->where('published_at','>=', now()->subWeek())
            ->orderByDesc('views_last7')
            ->limit(10)->get(['id','title','slug','views_last7']);

        foreach ($subs as $u) {
            \Mail::to($u->email)->queue(new \App\Mail\WeeklyDigest($u, $top));
        }
    }
}
