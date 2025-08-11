<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\ArticleRepositoryInterface::class,  \App\Repositories\EloquentArticleRepository::class);
        $this->app->bind(\App\Contracts\CategoryRepositoryInterface::class, \App\Repositories\EloquentCategoryRepository::class);
        $this->app->bind(\App\Contracts\TagRepositoryInterface::class,      \App\Repositories\EloquentTagRepository::class);
        $this->app->bind(\App\Contracts\CommentRepositoryInterface::class,  \App\Repositories\EloquentCommentRepository::class);
        $this->app->bind(\App\Contracts\ArticleRepositoryInterface::class, \App\Repositories\EloquentArticleRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
