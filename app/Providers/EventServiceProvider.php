<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Spatie\Activitylog\Models\Activity::saving(function ($activity) {
            if (auth()->check()) $activity->causer_id = auth()->id();
        });
    }
}
