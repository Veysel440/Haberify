<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Article\{BulkActionRequest, ScheduleRequest};
use App\Http\Resources\Api\V1\{ArticleCollection, ArticleResource};
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleAdminController extends Controller
{
    public function __construct(private ArticleService $svc)
    { $this->middleware(['auth:sanctum','permission:articles.update']); }

    public function index(Request $r)
    {
        $filters = $r->only(['status','category_id','language','featured','sort']);
        $perPage = $r->integer('per_page', 20);
        return new ArticleCollection($this->svc->list($filters, $perPage));
    }

    public function schedule(int $id, ScheduleRequest $r)
    {
        $a = $this->svc->update($id, [
            'status' => 'scheduled',
            'scheduled_at' => $r->validated()['scheduled_at'],
            'published_at' => null,
        ]);
        return new ArticleResource($a);
    }

    public function feature(int $id)
    {
        $a = $this->svc->update($id, ['is_featured' => true]);
        return new ArticleResource($a);
    }

    public function unfeature(int $id)
    {
        $a = $this->svc->update($id, ['is_featured' => false]);
        return new ArticleResource($a);
    }

    public function bulk(BulkActionRequest $r)
    {
        $data = $r->validated();
        $result = app(\App\Services\Admin\ArticleBulkService::class)->handle($data['ids'], $data['action']);
        return response()->json(['data'=>$result]);
    }
}
