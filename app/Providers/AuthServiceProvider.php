<?php

namespace App\Providers;

use App\Models\{Article, Category, Tag, Comment, Page, User};
use App\Policies\{
    ArticlePolicy, CategoryPolicy, TagPolicy, CommentPolicy,
    PagePolicy, MenuPolicy, SettingPolicy, UserPolicy
};
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Article::class  => ArticlePolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class      => TagPolicy::class,
        Comment::class  => CommentPolicy::class,
        Page::class     => PagePolicy::class,
        User::class     => UserPolicy::class,

    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        Gate::define('menus.edit', fn($user) => $user->can('menus.edit'));
        Gate::define('settings.manage', fn($user) => $user->can('settings.manage'));
        Gate::define('users.manage', fn($user) => $user->can('users.manage'));
    }
}
