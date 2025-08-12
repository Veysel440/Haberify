<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Services\ArticleService;
use App\Http\Requests\Api\V1\Article\StoreArticleRequest;
use App\Http\Requests\Api\V1\Article\UpdateArticleRequest;
use App\Http\Resources\Api\V1\{ArticleResource, ArticleCollection};
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $svc)
    { $this->middleware('auth:sanctum')->except(['index','show']); }

    public function index(Request $r)
    {
        $list = $this->svc->list($r->all(), $r->integer('per_page',15));
        return new ArticleCollection($list);
    }

    public function show(string $slug)
    {
        $a = $this->svc->findBySlug($slug) ?? abort(404);
        request()->attributes->set('article_id', $a->id);
        return new ArticleResource($a->load(['category','tags','author']));
    }

    public function store(StoreArticleRequest $r)
    {
        $data = $r->validated() + [
                'author_id'=>$r->user()->id,
                'slug'=>$r->input('slug') ?: \Str::slug($r->title),
            ];
        $a = $this->svc->create($data);
        return (new ArticleResource($a))->response()->setStatusCode(201);
    }

    public function update(int $id, UpdateArticleRequest $r)
    {
        $a = $this->svc->update($id, $r->validated());
        return new ArticleResource($a);
    }

    public function publish(int $id)
    {
        $a = $this->svc->publish($id);
        return new ArticleResource($a);
    }

    public function destroy(int $id)
    {
        $this->svc->delete($id);
        return response()->noContent();
    }
}
