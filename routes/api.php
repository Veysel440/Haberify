<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Comment\CommentLikeController;
use App\Http\Controllers\Api\Notification\NotificationController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/news.php';
require __DIR__ . '/api/comments.php';
require __DIR__ . '/api/admin.php';
require __DIR__ . '/api/categories.php';
require __DIR__ . '/api/tags.php';


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::post('/comments/{id}/like', [CommentLikeController::class, 'like']);
});
