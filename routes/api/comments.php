<?php

use App\Http\Controllers\Api\Comment\CommentController;
use App\Http\Controllers\Api\Comment\CommentLikeController;
use App\Http\Controllers\Api\Comment\CommentReportController;
use Illuminate\Support\Facades\Route;

Route::get('/comments',              [CommentController::class, 'index']);
Route::get('/comments/{id}',         [CommentController::class, 'show']);
Route::get('/comments/{id}/replies', [CommentController::class, 'replies']);
Route::get('/comments/{id}/like-stats', [CommentLikeController::class, 'stats']);

Route::middleware('auth:sanctum')->post('/comments/{id}/report', [CommentReportController::class, 'report']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments',         [CommentController::class, 'store']);
    Route::patch('/comments/{id}',   [CommentController::class, 'update']);
    Route::delete('/comments/{id}',  [CommentController::class, 'destroy']);
    Route::post('/comments/{id}/like', [CommentLikeController::class, 'like']);
    Route::get('/comment-reports',   [CommentReportController::class, 'index']);
    Route::delete('/comment-reports/{id}', [CommentReportController::class, 'destroy']);
});
