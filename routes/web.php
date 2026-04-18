<?php

declare(strict_types=1);

use App\Http\Controllers\RssController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rss', RssController::class);

Route::get('/sitemap.xml', SitemapController::class);
