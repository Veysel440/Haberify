<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\Api\V1\Category\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Http\Resources\Api\V1\CategoryResource;
use App\DTO\Category\{CreateCategoryData, UpdateCategoryData};
use App\Http\Responses\ApiResponse;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $svc)
    { $this->middleware('auth:sanctum')->except(['index','show']); }

    public function index()
    { return ApiResponse::ok(CategoryResource::collection($this->svc->all())->resolve()); }

    public function show(string $slug)
    {
        $c = $this->svc->findBySlug($slug) ?? abort(404);
        return ApiResponse::ok((new CategoryResource($c))->resolve());
    }

    public function store(StoreCategoryRequest $r)
    {
        $c = $this->svc->create(CreateCategoryData::from($r->validated()));
        return ApiResponse::created((new CategoryResource($c))->resolve());
    }

    public function update(int $id, UpdateCategoryRequest $r)
    {
        $c = $this->svc->update($id, UpdateCategoryData::from($r->validated()));
        return ApiResponse::ok((new CategoryResource($c))->resolve());
    }

    public function destroy(int $id)
    { $this->svc->delete($id); return ApiResponse::noContent(); }
}
