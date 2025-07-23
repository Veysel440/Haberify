<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;

Route::get('/public-categories',        [CategoryController::class, 'index']);
Route::get('/public-categories/{id}',   [CategoryController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
});
