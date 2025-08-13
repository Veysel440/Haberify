<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global middleware.
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\RequestIdMiddleware::class,
    ];

    /**
     * Route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Güvenlik başlıkları
            \App\Http\Middleware\SecurityHeaders::class,
        ],

        'api' => [
            // API yanıtı JSON, dil, güvenlik
            \App\Http\Middleware\ForceJsonResponse::class,
            \App\Http\Middleware\SetLocaleFromHeader::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\ValidateJson::class,
            \App\Http\Middleware\SentryContext::class,

            // Session tabanlı analytics (sid)
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,

            // Rate limit (rol bazlı)
            \App\Http\Middleware\ThrottlePerRole::class.':api,120,60',

            // ETag ve binding
            \App\Http\Middleware\CacheHeadersMiddleware::class,
            \App\Http\Middleware\ETagMiddleware::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // Görüntüleme ve referrer takibi
            \App\Http\Middleware\TrackArticleView::class,
            \App\Http\Middleware\TrackReferrer::class,
        ],
    ];

    /**
     * Route middleware aliases.
     */
    protected $middlewareAliases = [
        'auth'              => \App\Http\Middleware\Authenticate::class,
        'auth.basic'        => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session'      => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers'     => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can'               => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'             => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm'  => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'            => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'          => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'          => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,


        'role'        => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission'  => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        'roles_or'    => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class,


        'comment.notbanned' => \App\Http\Middleware\EnsureNotCommentBanned::class,
    ];
}
