<?php

use App\Http\Controllers\Api\AdminStatsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/approve-comment/{id}', [AdminController::class, 'approveComment']);
    Route::post('/reject-comment/{id}',  [AdminController::class, 'rejectComment']);
    Route::post('/news/{id}/feature',    [AdminController::class, 'makeFeatured']);
    Route::post('/news/{id}/unpublish',  [AdminController::class, 'unpublishNews']);
    Route::get('/users',                 [AdminController::class, 'users']);
    Route::post('/make-admin/{id}',      [AdminController::class, 'makeAdmin']);
    Route::post('/users/{id}/suspend', [AdminController::class, 'suspendUser']);
    Route::post('/users/{id}/activate', [AdminController::class, 'activateUser']);
});
Route::middleware(['auth:sanctum', 'admin'])->get('/stats', [AdminStatsController::class, 'index']);
Route::middleware(['auth:sanctum', 'admin'])->get('/admin/stats', [AdminController::class, 'stats']);
Route::middleware(['auth:sanctum', 'admin'])->get('/admin/comments', [AdminController::class, 'allComments']);
Route::middleware(['auth:sanctum', 'admin'])->post('/admin/comments/{id}/approve', [AdminController::class, 'approveComment']);
Route::middleware(['auth:sanctum', 'admin'])->delete('/admin/comments/{id}', [AdminController::class, 'deleteComment']);
