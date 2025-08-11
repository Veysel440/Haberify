<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Http\Requests\Api\V1\Category\{StoreCategoryRequest, UpdateCategoryRequest};
use App\Http\Resources\Api\V1\CategoryResource;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $svc)
    { $this->middleware('auth:sanctum')->except(['index','show']); }

    public function index()
    { return CategoryResource::collection($this->svc->all()); }

    public function show(string $slug)
    { return new CategoryResource($this->svc->findBySlug($slug) ?? abort(404)); }

    public function store(StoreCategoryRequest $r)
    { return (new CategoryResource($this->svc->create($r->validated())))->response()->setStatusCode(201); }

    public function update(int $id, UpdateCategoryRequest $r)
    { return new CategoryResource($this->svc->update($id, $r->validated())); }

    public function destroy(int $id)
    { $this->svc->delete($id); return response()->noContent(); }
}
