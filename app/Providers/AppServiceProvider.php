<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Log;
use PragmaRX\Google2FA\Google2FA;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\ArticleRepositoryInterface::class, \App\Repositories\EloquentArticleRepository::class);
        $this->app->bind(\App\Contracts\CategoryRepositoryInterface::class, \App\Repositories\EloquentCategoryRepository::class);
        $this->app->bind(\App\Contracts\TagRepositoryInterface::class, \App\Repositories\EloquentTagRepository::class);
        $this->app->bind(\App\Contracts\CommentRepositoryInterface::class, \App\Repositories\EloquentCommentRepository::class);
        $this->app->bind(\App\Contracts\ArticleRepositoryInterface::class, \App\Repositories\EloquentArticleRepository::class);

        // Single Google2FA instance — stateless, safe to share.
        $this->app->singleton(Google2FA::class);
    }

    public function boot(): void
    {
        Queue::failing(function ($event) {
            if (app()->bound('sentry')) {
                \Sentry\captureException($event->exception);
            }
            Log::error('queue.failed', [
                'job' => $event->job?->getName(),
                'connection' => $event->connectionName,
                'exception' => $event->exception->getMessage(),
            ]);
        });
    }
}
