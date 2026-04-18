<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\Tag\CreateTagData;
use App\DTO\Tag\UpdateTagData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Tag\StoreTagRequest;
use App\Http\Requests\Api\V1\Tag\UpdateTagRequest;
use App\Http\Resources\Api\V1\TagResource;
use App\Http\Responses\ApiResponse;
use App\Services\TagService;

class TagController extends Controller
{
    public function __construct(private TagService $svc)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        return ApiResponse::ok(TagResource::collection($this->svc->all())->resolve());
    }

    public function show(string $slug)
    {
        return ApiResponse::ok((new TagResource($this->svc->findBySlug($slug) ?? abort(404)))->resolve());
    }

    public function store(StoreTagRequest $r)
    {
        return ApiResponse::created((new TagResource($this->svc->create(CreateTagData::from($r->validated()))))->resolve());
    }

    public function update(int $id, UpdateTagRequest $r)
    {
        return ApiResponse::ok((new TagResource($this->svc->update($id, UpdateTagData::from($r->validated()))))->resolve());
    }

    public function destroy(int $id)
    {
        $this->svc->delete($id);

        return ApiResponse::noContent();
    }
}
