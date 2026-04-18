<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\Category\CreateCategoryData;
use App\DTO\Category\UpdateCategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Category\StoreCategoryRequest;
use App\Http\Requests\Api\V1\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Responses\ApiResponse;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $svc)
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index()
    {
        return ApiResponse::ok(CategoryResource::collection($this->svc->all())->resolve());
    }

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
    {
        $this->svc->delete($id);

        return ApiResponse::noContent();
    }
}
