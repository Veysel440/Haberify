<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $services = [
            \App\Services\NewsServiceInterface::class    => \App\Services\NewsService::class,
            \App\Services\CategoryServiceInterface::class=> \App\Services\CategoryService::class,
            \App\Services\CommentServiceInterface::class => \App\Services\CommentService::class,
            \App\Services\TagServiceInterface::class     => \App\Services\TagService::class,
            \App\Services\AdminServiceInterface::class   => \App\Services\AdminService::class,
            \App\Services\UserServiceInterface::class => \App\Services\UserService::class,
            \App\Services\CommentLikeServiceInterface::class  =>  \App\Services\CommentLikeService::class,
            \App\Services\CommentReportServiceInterface::class => \App\Services\CommentReportService::class,

        ];

        foreach ($services as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    public function boot(): void
    {
        //
    }
}
