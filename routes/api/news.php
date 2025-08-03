<?php

use App\Http\Controllers\Api\News\NewsController;
use App\Http\Controllers\Api\News\NewsHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/public-news',           [NewsController::class, 'index']);
Route::get('/public-news/{id}',      [NewsController::class, 'show']);
Route::get('/news-by-category/{categoryId}', [NewsController::class, 'byCategory']);
Route::get('/news/popular',          [NewsController::class, 'popular']);
Route::get('/news/featured',         [NewsController::class, 'featured']);
Route::get('/news/{id}/history',     [NewsHistoryController::class, 'index']);
Route::get('/news/search',           [NewsController::class, 'search']);
Route::get('/news/{id}/meta',        [NewsController::class, 'meta']);

Route::middleware('auth:sanctum')->post('/news/{id}/gallery', [NewsController::class, 'uploadGallery']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/news/upload-image', [NewsController::class, 'uploadImage']);
    Route::apiResource('news', NewsController::class)->except(['index', 'show']);
});
