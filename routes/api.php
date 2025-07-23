<?php

use App\Http\Controllers\Api\CommentLikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

require __DIR__ . '/api/news.php';
require __DIR__ . '/api/comments.php';
require __DIR__ . '/api/admin.php';
require __DIR__ . '/api/categories.php';
require __DIR__ . '/api/tags.php';


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
});
Route::middleware('auth:sanctum')->post('/comments/{id}/like', [CommentLikeController::class, 'like']);
