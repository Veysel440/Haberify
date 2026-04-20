<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Domain event wiring.
 *
 * Previously this provider attached a manual `Activity::saving` listener
 * that wrote `causer_id = auth()->id()` on every activity log row. That
 * listener was redundant: spatie/laravel-activitylog's `ActivityLogger`
 * already calls `causedBy($causerResolver->resolve())` for every log call
 * (see vendor/spatie/laravel-activitylog/src/ActivityLogger.php:226), and
 * `CauserResolver::getDefaultCauser` returns `auth()->user()` by default.
 *
 * Keeping the manual hook only opened the door to double-writing the
 * causer and would have masked a real bug if the trait ever stopped
 * resolving the causer correctly. Removed.
 */
class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
