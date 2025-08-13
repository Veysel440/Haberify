<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CommentService;
use App\Http\Requests\Api\V1\Comment\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;
use App\DTO\Comment\CreateCommentData;
use App\Http\Responses\ApiResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $svc) {}

    public function index(int $articleId)
    { return ApiResponse::ok(CommentResource::collection($this->svc->listForArticle($articleId))->resolve()); }

    public function store(int $articleId, StoreCommentRequest $r)
    {
        $v = $r->validated() + [
                'article_id'=>$articleId,
                'user_id'=>$r->user()?->id,
                'ip'=>$r->ip(),
                'ua'=>$r->userAgent(),
            ];
        $c = $this->svc->create(CreateCommentData::from($v));
        return ApiResponse::created((new CommentResource($c))->resolve());
    }

    public function approve(int $id)
    { return ApiResponse::ok((new CommentResource($this->svc->setStatus($id,'approved')))->resolve()); }

    public function reject(int $id)
    { return ApiResponse::ok((new CommentResource($this->svc->setStatus($id,'rejected')))->resolve()); }

    public function destroy(int $id)
    { $this->svc->delete($id); return ApiResponse::noContent(); }
}
