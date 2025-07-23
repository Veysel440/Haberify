<?php

use App\Http\Controllers\Api\CommentLikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;


Route::get('/comments',        [CommentController::class, 'index']);
Route::get('/comments/{id}',   [CommentController::class, 'show']);
Route::get('/comments/{id}/like-stats', [CommentLikeController::class, 'stats']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
});
