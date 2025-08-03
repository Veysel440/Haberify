<?php

use App\Http\Controllers\Api\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/public-categories',        [CategoryController::class, 'index']);
Route::get('/public-categories/{id}',   [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
});
