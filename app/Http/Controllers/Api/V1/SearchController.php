<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $r)
    {
        $q = trim((string)$r->query('q',''));
        abort_if($q === '', 422, 'Query required');

        $results = Article::search($q)
            ->when($lang = $r->query('language'), fn($b)=>$b->where('language',$lang))
            ->take(50)->get();

        // eager load for resource
        $results->load(['category','tags','author']);
        return ArticleResource::collection($results);
    }
}
