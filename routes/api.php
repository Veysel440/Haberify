<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    ArticleController,
    AuditController,
    CategoryController,
    CommentController,
    ExportController,
    ImportController,
    MediaController,
    MenuController,
    NotificationController,
    PageController,
    SettingController,
    TagController,
    TwoFactorController,
    AnalyticsController,
    SearchController
};
use App\Http\Controllers\Api\V1\Admin\{
    ArticleAdminController,
    CommentAdminController,
    UserAdminController
};

Route::prefix('v1')->group(function () {
    // Public
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/{slug}', [TagController::class, 'show'])->name('tags.show');

    Route::get('pages/{slug}', [PageController::class,'show'])->name('pages.show');

    Route::get('menus/{name}', [MenuController::class,'show'])->name('menus.show');

    // Comments (public create ok, list ok)
    Route::get('articles/{articleId}/comments', [CommentController::class, 'index'])
        ->whereNumber('articleId')->name('comments.index');
    Route::post('articles/{articleId}/comments', [CommentController::class, 'store'])
        ->whereNumber('articleId')->name('comments.store');

    // Auth + 2FA (login flow)
    Route::post('auth/login', [TwoFactorController::class,'verifyLogin'])->name('auth.login');
    Route::post('auth/2fa/verify', [TwoFactorController::class,'verifyCode'])->name('auth.2fa.verify');

    // Search
    Route::get('search', SearchController::class)->name('search');

    // Authenticated zone
    Route::middleware('auth:sanctum')->group(function () {
        // Notifications
        Route::get('notifications', [NotificationController::class,'index'])->name('notifications.index');
        Route::get('notifications/unread-count', [NotificationController::class,'unreadCount'])->name('notifications.unreadCount');
        Route::post('notifications/{id}/read', [NotificationController::class,'markAsRead'])->name('notifications.read');

        // Media (articles)
        Route::post('articles/{id}/cover',   [MediaController::class,'uploadCover'])
            ->middleware('permission:articles.update')->whereNumber('id')->name('articles.cover');
        Route::post('articles/{id}/gallery', [MediaController::class,'uploadGallery'])
            ->middleware('permission:articles.update')->whereNumber('id')->name('articles.gallery');

        // Articles
        Route::post('articles', [ArticleController::class, 'store'])
            ->middleware('permission:articles.create')->name('articles.store');
        Route::put('articles/{id}', [ArticleController::class, 'update'])
            ->middleware('permission:articles.update')->whereNumber('id')->name('articles.update');
        Route::post('articles/{id}/publish', [ArticleController::class, 'publish'])
            ->middleware('permission:articles.publish')->whereNumber('id')->name('articles.publish');
        Route::delete('articles/{id}', [ArticleController::class, 'destroy'])
            ->middleware('permission:articles.delete')->whereNumber('id')->name('articles.destroy');

        // Categories
        Route::post('categories', [CategoryController::class, 'store'])
            ->middleware('permission:categories.manage')->name('categories.store');
        Route::put('categories/{id}', [CategoryController::class, 'update'])
            ->middleware('permission:categories.manage')->whereNumber('id')->name('categories.update');
        Route::delete('categories/{id}', [CategoryController::class, 'destroy'])
            ->middleware('permission:categories.manage')->whereNumber('id')->name('categories.destroy');

        // Tags
        Route::post('tags', [TagController::class, 'store'])
            ->middleware('permission:tags.manage')->name('tags.store');
        Route::put('tags/{id}', [TagController::class, 'update'])
            ->middleware('permission:tags.manage')->whereNumber('id')->name('tags.update');
        Route::delete('tags/{id}', [TagController::class, 'destroy'])
            ->middleware('permission:tags.manage')->whereNumber('id')->name('tags.destroy');

        // Comments moderation
        Route::post('comments/{id}/approve', [CommentController::class, 'approve'])
            ->middleware('permission:comments.moderate')->whereNumber('id')->name('comments.approve');
        Route::post('comments/{id}/reject', [CommentController::class, 'reject'])
            ->middleware('permission:comments.moderate')->whereNumber('id')->name('comments.reject');
        Route::delete('comments/{id}', [CommentController::class, 'destroy'])
            ->middleware('permission:comments.moderate')->whereNumber('id')->name('comments.destroy');

        // Menus
        Route::put('menus/{name}', [MenuController::class,'update'])
            ->middleware('permission:menus.edit')->name('menus.update');

        // Settings
        Route::get('settings/{key}', [SettingController::class,'show'])->name('settings.show');
        Route::put('settings/{key}', [SettingController::class,'update'])
            ->middleware('permission:settings.manage')->name('settings.update');

        // Pages manage
        Route::post('pages', [PageController::class,'store'])
            ->middleware('permission:pages.manage')->name('pages.store');
        Route::put('pages/{id}', [PageController::class,'update'])
            ->middleware('permission:pages.manage')->whereNumber('id')->name('pages.update');
        Route::delete('pages/{id}', [PageController::class,'destroy'])
            ->middleware('permission:pages.manage')->whereNumber('id')->name('pages.destroy');

        // Analytics
        Route::prefix('analytics')->middleware('permission:analytics.view')->group(function () {
            Route::get('overview', [AnalyticsController::class,'overview'])->name('analytics.overview');
            Route::get('top-articles', [AnalyticsController::class,'topArticles'])->name('analytics.top');
            Route::get('referrers', [AnalyticsController::class,'referrers'])->name('analytics.referrers');
            Route::get('category-share', [AnalyticsController::class,'categoryShare'])->name('analytics.categoryShare');
        });

        // Export / Import
        Route::get('exports/articles.csv', [ExportController::class,'articlesCsv'])->name('exports.articles');
        Route::post('imports/categories', [ImportController::class,'categories'])
            ->middleware('permission:categories.manage')->name('imports.categories');

        // Audit
        Route::get('audit', [AuditController::class,'index'])
            ->middleware('permission:analytics.view')->name('audit.index');

        // 2FA manage
        Route::prefix('auth/2fa')->group(function(){
            Route::post('enable', [TwoFactorController::class,'enable'])->name('auth.2fa.enable');
            Route::get('qrcode',  [TwoFactorController::class,'qrcode'])->name('auth.2fa.qrcode');
            Route::post('disable',[TwoFactorController::class,'disable'])->name('auth.2fa.disable');
        });

        Route::prefix('admin')->group(function () {

            // Articles admin
            Route::get('articles', [ArticleAdminController::class,'index'])->middleware('permission:articles.update');
            Route::post('articles/{id}/schedule', [ArticleAdminController::class,'schedule'])
                ->middleware('permission:articles.publish')->whereNumber('id');
            Route::post('articles/{id}/feature', [ArticleAdminController::class,'feature'])
                ->middleware('permission:articles.update')->whereNumber('id');
            Route::post('articles/{id}/unfeature', [ArticleAdminController::class,'unfeature'])
                ->middleware('permission:articles.update')->whereNumber('id');
            Route::post('articles/bulk', [ArticleAdminController::class,'bulk'])
                ->middleware('permission:articles.update');

            // Comments admin
            Route::post('comments/ban', [CommentAdminController::class,'ban'])->middleware('permission:comments.moderate');
            Route::post('comments/unban/{userId}', [CommentAdminController::class,'unban'])
                ->middleware('permission:comments.moderate')->whereNumber('userId');

            // Users admin
            Route::get('users', [UserAdminController::class,'index']);
            Route::post('users/{id}/assign-role', [UserAdminController::class,'assignRole'])->whereNumber('id');
        });
    });
});
