<?php

namespace App\Http\Controllers\Api\Category;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Services\Category\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        try {
            $categories = $this->categoryService->list();
            return ApiResponse::success(
                CategoryResource::collection($categories),
                "Kategoriler listelendi"
            );
        } catch (Exception $e) {
            return ApiResponse::error("Kategoriler listelenemedi", 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryService->find($id);
            return ApiResponse::success(
                new CategoryResource($category),
                "Kategori detay"
            );
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kategori bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Kategori gösterilemedi", 500);
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $category = $this->categoryService->create($data);

            return ApiResponse::success(
                new CategoryResource($category),
                "Kategori eklendi",
                201
            );
        } catch (Exception $e) {
            return ApiResponse::error("Kategori eklenemedi", 500);
        }
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $category = $this->categoryService->update($id, $data);

            return ApiResponse::success(
                new CategoryResource($category),
                "Kategori güncellendi"
            );
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
