<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use App\Http\Requests\Api\V1\Article\{StoreArticleRequest, UpdateArticleRequest};
use App\Http\Resources\Api\V1\{ArticleResource, ArticleCollection};
use App\DTO\Article\{CreateArticleData, UpdateArticleData};
use App\Http\Responses\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $svc)
    { $this->middleware('auth:sanctum')->except(['index','show']); }

    public function index(Request $r)
    {
        $list = $this->svc->list($r->all(), per_page($r));
        return ApiResponse::ok((new ArticleCollection($list))->toArray($r));
    }

    public function show(string $slug)
    {
        $a = $this->svc->findBySlug($slug) ?? abort(404);
        request()->attributes->set('article_id', $a->id);
        return ApiResponse::ok((new ArticleResource($a->load(['category','tags','author'])))->toArray(request()));
    }

    public function store(StoreArticleRequest $r)
    {
        $v = $r->validated() + [
                'author_id'=>$r->user()->id,
                'slug'=> $r->input('slug') ?: Str::slug($r->title),
            ];
        $a = $this->svc->create(CreateArticleData::from($v));
        return ApiResponse::created((new ArticleResource($a))->toArray($r));
    }

    public function update(int $id, UpdateArticleRequest $r)
    {
        $a = $this->svc->update($id, UpdateArticleData::from($r->validated()));
        return ApiResponse::ok((new ArticleResource($a))->toArray($r));
    }

    public function publish(int $id)
    {
        $a = $this->svc->publish($id);
        return ApiResponse::ok((new ArticleResource($a))->toArray(request()));
    }

    public function destroy(int $id)
    {
        $this->svc->delete($id);
        return ApiResponse::noContent();
    }
}
