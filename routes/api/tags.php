<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;


Route::get('/public-tags', [TagController::class, 'index']);


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tags', TagController::class)->except(['index', 'show']);
});
