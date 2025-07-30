<?php

use App\Http\Controllers\Api\CommentLikeController;
use App\Http\Controllers\Api\CommentReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;


Route::get('/comments',        [CommentController::class, 'index']);
Route::get('/comments/{id}',   [CommentController::class, 'show']);
Route::get('/comments/{id}/like-stats', [CommentLikeController::class, 'stats']);
Route::middleware('auth:sanctum')->post('/comments/{id}/report', [CommentReportController::class, 'report']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::get('/comment-reports', [CommentReportController::class, 'index']);
    Route::delete('/comment-reports/{id}', [CommentReportController::class, 'destroy']);
});
Route::get('/comments/{id}/replies', [CommentController::class, 'replies']);
