<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\News\NewsService;
use App\Services\Category\CategoryService;
use App\Services\Comment\CommentService;
use App\Services\Tag\TagService;
use App\Services\Admin\AdminService;
use App\Services\User\UserService;
use App\Services\Comment\CommentLikeService;
use App\Services\Comment\CommentReportService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('news.service', NewsService::class);
        $this->app->bind('category.service', CategoryService::class);
        $this->app->bind('comment.service', CommentService::class);
        $this->app->bind('tag.service', TagService::class);
        $this->app->bind('admin.service', AdminService::class);
        $this->app->bind('user.service', UserService::class);
        $this->app->bind('comment_like.service', CommentLikeService::class);
        $this->app->bind('comment_report.service', CommentReportService::class);

    }

    public function boot(): void
    {
        //
    }
}
