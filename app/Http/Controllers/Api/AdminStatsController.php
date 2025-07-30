<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\News;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function index()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_news' => News::count(),
            'total_comments' => Comment::count(),
            'top_news' => News::orderByDesc('views')->take(5)->get(['id','title','views']),
            'most_favorited_news' => News::withCount('favorites')->orderByDesc('favorites_count')->take(5)->get(['id','title']),
            'comments_last_7days' => Comment::where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }
}
