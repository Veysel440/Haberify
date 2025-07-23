<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Services\TagServiceInterface;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TagController extends Controller
{
    private TagServiceInterface $tagService;

    public function __construct(TagServiceInterface $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        try {
            $tags = $this->tagService->list();
            return ApiResponse::success(TagResource::collection($tags), "Etiketler listelendi");
        } catch (Exception $e) {
            return ApiResponse::error("Etiketler listelenemedi", 500);
        }
    }

    public function show($id)
    {
        try {
            $tag = $this->tagService->find($id);
            return ApiResponse::success(new TagResource($tag), "Etiket detay");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Etiket gösterilemedi", 500);
        }
    }

    public function store(StoreTagRequest $request)
    {
        try {
            $tag = $this->tagService->create($request->validated());
            return ApiResponse::success(new TagResource($tag), "Etiket eklendi", 201);
        } catch (Exception $e) {
            return ApiResponse::error("Etiket eklenemedi", 500);
        }
    }

    public function update(UpdateTagRequest $request, $id)
    {
        try {
            $tag = $this->tagService->update($id, $request->validated());
            return ApiResponse::success(new TagResource($tag), "Etiket güncellendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Etiket güncellenemedi", 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->tagService->delete($id);
            return ApiResponse::success(null, "Etiket silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Etiket silinemedi", 500);
        }
    }
}
