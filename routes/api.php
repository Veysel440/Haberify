<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{ArticleController,
    CategoryController,
    NotificationController,
    TagController,
    CommentController};

Route::prefix('v1')->group(function () {

    Route::get('notifications', [NotificationController::class,'index']);
    Route::get('notifications/unread-count', [NotificationController::class,'unreadCount']);
    Route::post('notifications/{id}/read', [NotificationController::class,'markAsRead']);
    // Public
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/{slug}', [TagController::class, 'show'])->name('tags.show');

    Route::get('articles/{articleId}/comments', [CommentController::class, 'index'])
        ->whereNumber('articleId')->name('comments.index');
    Route::post('articles/{articleId}/comments', [CommentController::class, 'store'])
        ->middleware('auth:sanctum')->whereNumber('articleId')->name('comments.store');

    // Authenticated + permissions
    Route::middleware('auth:sanctum')->group(function () {
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
    });
});
