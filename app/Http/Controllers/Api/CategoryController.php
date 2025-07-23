<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryServiceInterface;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoryController extends Controller
{
    private CategoryServiceInterface $categoryService;

    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $categories = $this->categoryService->list();
            return ApiResponse::success(CategoryResource::collection($categories), "Kategoriler listelendi");
        } catch (Exception $e) {
            return ApiResponse::error("Kategoriler listelenemedi", 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->find($id);
            return ApiResponse::success(new CategoryResource($category), "Kategori detay");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kategori bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Kategori gösterilemedi", 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $category = $this->categoryService->create($request->validated());
            return ApiResponse::success(new CategoryResource($category), "Kategori eklendi", 201);
        } catch (Exception $e) {
            return ApiResponse::error("Kategori eklenemedi", 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryService->update($id, $request->validated());
            return ApiResponse::success(new CategoryResource($category), "Kategori güncellendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kategori bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Kategori güncellenemedi", 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);
            return ApiResponse::success(null, "Kategori silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kategori bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Kategori silinemedi", 500);
        }
    }
}
