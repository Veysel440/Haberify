<?php

use App\Http\Controllers\Api\NewsHistoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewsController;


Route::get('/public-news',        [NewsController::class, 'index']);
Route::get('/public-news/{id}',   [NewsController::class, 'show']);
Route::get('/news-by-category/{categoryId}', [NewsController::class, 'byCategory']);
Route::get('/news/popular',       [NewsController::class, 'popular']);
Route::get('/news/featured',      [NewsController::class, 'featured']);
Route::get('/news/{id}/history', [NewsHistoryController::class, 'index']);
Route::post('/news/{id}/gallery', [NewsController::class, 'uploadGallery'])
    ->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/news/upload-image', [NewsController::class, 'uploadImage']);
    Route::apiResource('news', NewsController::class)->except(['index', 'show']);
});

Route::get('/news/search', [NewsController::class, 'search']);
Route::get('/news/{id}/meta', [NewsController::class, 'meta']);
