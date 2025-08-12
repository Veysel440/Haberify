<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:analytics.view']); }

    public function overview()
    {
        $days = match(request('range','7d')){ '30d'=>30, '90d'=>90, default=>7 };
        $from = now()->subDays($days)->toDateString();

        $trend = DB::table('article_view_daily')
            ->selectRaw('day, SUM(views) as views')
            ->where('day','>=',$from)->groupBy('day')->orderBy('day')->get();

        $kpis = [
            'articles' => DB::table('articles')->where('status','published')
                ->whereDate('published_at','>=',$from)->count(),
            'comments' => DB::table('comments')->where('status','approved')
                ->whereDate('created_at','>=',$from)->count(),
            'views'    => DB::table('article_view_daily')->where('day','>=',$from)->sum('views'),
        ];

        return response()->json(['data'=>compact('kpis','trend')]);
    }

    public function topArticles()
    {
        $days = match(request('range','7d')){ '30d'=>30, '90d'=>90, default=>7 };
        $from = now()->subDays($days)->toDateString();

        $top = DB::table('article_view_daily as d')
            ->join('articles as a','a.id','=','d.article_id')
            ->selectRaw('a.id,a.slug,a.title,SUM(d.views) views')
            ->where('d.day','>=',$from)
            ->groupBy('a.id','a.slug','a.title')
            ->orderByDesc('views')->limit(20)->get();

        return response()->json(['data'=>$top]);
    }

    public function referrers()
    {
        $days = match(request('range','7d')){ '30d'=>30, '90d'=>90, default=>7 };
        $from = now()->subDays($days)->toDateString();

        $list = DB::table('visits')
            ->selectRaw('COALESCE(ref,"direct") ref, COUNT(*) c')
            ->whereDate('created_at','>=',$from)
            ->groupBy('ref')->orderByDesc('c')->limit(20)->get();

        return response()->json(['data'=>$list]);
    }

    public function categoryShare()
    {
        $days = match(request('range','7d')){ '30d'=>30, '90d'=>90, default=>7 };
        $from = now()->subDays($days)->toDateString();

        $share = DB::table('article_view_daily as d')
            ->join('articles as a','a.id','=','d.article_id')
            ->join('categories as c','c.id','=','a.category_id')
            ->selectRaw('c.name, SUM(d.views) views')
            ->where('d.day','>=',$from)
            ->groupBy('c.name')->orderByDesc('views')->get();

        return response()->json(['data'=>$share]);
    }
}
