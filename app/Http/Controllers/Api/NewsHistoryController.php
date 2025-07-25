<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Http\Resources\NewsHistoryResource;

class NewsHistoryController extends Controller
{
    public function index($newsId)
    {
        $news = News::findOrFail($newsId);
        $histories = $news->histories()->with('editor')->latest()->get();
        return NewsHistoryResource::collection($histories);
    }
}
