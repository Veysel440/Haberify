<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\MenuPolicy;
use App\Policies\PagePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Authorisation wiring.
 *
 * Permission resolution (`$user->can('foo')`) is handled transparently by
 * spatie/laravel-permission via its own `Gate::before` hook registered in
 * `Spatie\Permission\PermissionRegistrar::registerPermissions`. That means
 * we do NOT need (and must not add) explicit `Gate::define('foo', fn () =>
 * $user->can('foo'))` bridges — they would be infinite-recursion traps for
 * any user that does not hold the ability.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        Category::class => CategoryPolicy::class,
        Tag::class => TagPolicy::class,
        Comment::class => CommentPolicy::class,
        Page::class => PagePolicy::class,
        User::class => UserPolicy::class,
        Setting::class => SettingPolicy::class,
        Menu::class => MenuPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Admins bypass every ability check. Other users fall through so
        // Spatie's own `Gate::before` (registered by PermissionServiceProvider)
        // can resolve the ability against the permission table.
        //
        // IMPORTANT: `$user` is nullable because Gate::before fires for
        // unauthenticated callers too (Gate::allows('foo') with a guest).
        // Calling `hasRole(...)` on null would fatal.
        Gate::before(function (?User $user): ?bool {
            if ($user === null) {
                return null;
            }

            return $user->hasRole('admin') ? true : null;
        });
    }
}
