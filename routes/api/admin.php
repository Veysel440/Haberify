<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AdminStatsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/approve-comment/{id}',      [AdminController::class, 'approveComment']);
    Route::post('/reject-comment/{id}',       [AdminController::class, 'rejectComment']);
    Route::post('/news/{id}/feature',         [AdminController::class, 'makeFeatured']);
    Route::post('/news/{id}/unpublish',       [AdminController::class, 'unpublishNews']);

    Route::get('/users',                      [AdminController::class, 'users']);
    Route::post('/make-admin/{id}',           [AdminController::class, 'makeAdmin']);
    Route::post('/users/{id}/suspend',        [AdminController::class, 'suspendUser']);
    Route::post('/users/{id}/activate',       [AdminController::class, 'activateUser']);

    Route::get('/stats',                      [AdminStatsController::class, 'index']);
    Route::get('/comments',                   [AdminController::class, 'allComments']);
    Route::post('/comments/{id}/approve',     [AdminController::class, 'approveComment']);
    Route::delete('/comments/{id}',           [AdminController::class, 'deleteComment']);
});
