<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CommentService;
use App\Http\Requests\Api\V1\Comment\StoreCommentRequest;
use App\Http\Resources\Api\V1\CommentResource;

class CommentController extends Controller
{
    public function __construct(private CommentService $svc) {}

    public function index(int $articleId)
    { return CommentResource::collection($this->svc->listForArticle($articleId)); }

    public function store(int $articleId, StoreCommentRequest $r)
    {
        $c = $this->svc->create($articleId, $r->validated() + ['user_id'=>$r->user()?->id]);
        return (new CommentResource($c))->response()->setStatusCode(201);
    }

    public function approve(int $id)
    { return new CommentResource($this->svc->setStatus($id,'approved')); }

    public function reject(int $id)
    { return new CommentResource($this->svc->setStatus($id,'rejected')); }

    public function destroy(int $id)
    { $this->svc->delete($id); return response()->noContent(); }
}
