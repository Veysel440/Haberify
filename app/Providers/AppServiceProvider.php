<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
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
        $this->configurePasswordPolicy();

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

    /**
     * Single source of truth for the password policy.
     *
     * Every write path (registration, password reset, admin-initiated
     * password set) must validate with `Password::defaults()` so tightening
     * the policy here propagates everywhere automatically.
     *
     * Config lives in `config/security.php` → `password.*`.
     */
    private function configurePasswordPolicy(): void
    {
        Password::defaults(function (): Password {
            /** @var array{
             *     min_length: int,
             *     require_mixed_case: bool,
             *     require_numbers: bool,
             *     require_symbols: bool,
             *     uncompromised_check: bool,
             *     uncompromised_threshold: int,
             * } $policy
             */
            $policy = config('security.password');

            $rule = Password::min($policy['min_length'])->letters();

            if ($policy['require_mixed_case']) {
                $rule = $rule->mixedCase();
            }

            if ($policy['require_numbers']) {
                $rule = $rule->numbers();
            }

            if ($policy['require_symbols']) {
                $rule = $rule->symbols();
            }

            if ($policy['uncompromised_check']) {
                $rule = $rule->uncompromised($policy['uncompromised_threshold']);
            }

            return $rule;
        });
    }
}
