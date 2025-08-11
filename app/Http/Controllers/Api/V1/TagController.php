<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use App\Http\Requests\Api\V1\Tag\{StoreTagRequest, UpdateTagRequest};
use App\Http\Resources\Api\V1\TagResource;

class TagController extends Controller
{
    public function __construct(private TagService $svc)
    { $this->middleware('auth:sanctum')->except(['index','show']); }

    public function index()
    { return TagResource::collection($this->svc->all()); }

    public function show(string $slug)
    { return new TagResource($this->svc->findBySlug($slug) ?? abort(404)); }

    public function store(StoreTagRequest $r)
    { return (new TagResource($this->svc->create($r->validated())))->response()->setStatusCode(201); }

    public function update(int $id, UpdateTagRequest $r)
    { return new TagResource($this->svc->update($id, $r->validated())); }

    public function destroy(int $id)
    { $this->svc->delete($id); return response()->noContent(); }
}
