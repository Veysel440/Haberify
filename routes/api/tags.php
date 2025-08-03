<?php

use App\Http\Controllers\Api\Tag\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/public-tags', [TagController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tags', TagController::class)->except(['index', 'show']);
});
