<?php

namespace App\Http\Controllers\Api\Tag;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\Tag\TagResource;
use App\Services\Tag\TagService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TagController extends Controller
{
    private TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index()
    {
        try {
            $tags = $this->tagService->list();
            return ApiResponse::success(TagResource::collection($tags), "Etiketler listelendi");
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiketler listelenemedi", 500);
        }
    }

    public function show(int $id)
    {
        try {
            $tag = $this->tagService->find($id);
            return ApiResponse::success(new TagResource($tag), "Etiket detay");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiket gösterilemedi", 500);
        }
    }

    public function showBySlug(string $slug)
    {
        try {
            $tag = $this->tagService->findBySlug($slug);
            return ApiResponse::success(new TagResource($tag), "Slug ile etiket detay");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı.", 404);
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiket gösterilemedi.", 500);
        }
    }

    public function store(StoreTagRequest $request)
    {
        try {
            $tag = $this->tagService->create($request->validated());
            return ApiResponse::success(new TagResource($tag), "Etiket eklendi", 201);
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiket eklenemedi", 500);
        }
    }

    public function update(UpdateTagRequest $request, int $id)
    {
        try {
            $tag = $this->tagService->update($id, $request->validated());
            return ApiResponse::success(new TagResource($tag), "Etiket güncellendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiket güncellenemedi", 500);
        }
    }

    public function trending(Request $request)
    {
        try {
            $validated = $request->validate([
                'limit' => 'nullable|integer|min:1|max:100',
            ]);
            $limit = $validated['limit'] ?? 10;
            $tags = $this->tagService->trendingTags($limit);
            return ApiResponse::success(TagResource::collection($tags), "Popüler etiketler");
        } catch (ValidationException $e) {
            return ApiResponse::error("Limit hatalı.", 422, $e->errors());
        } catch (\Throwable $e) {
            return ApiResponse::error("Popüler etiketler getirilemedi.", 500);
        }
    }

    public function trendingByDate(Request $request)
    {
        try {
            $validated = $request->validate([
                'from'  => 'required|date',
                'to'    => 'required|date|after_or_equal:from',
                'limit' => 'nullable|integer|min:1|max:100'
            ]);

            $from = $validated['from'];
            $to   = $validated['to'];
            $limit = $validated['limit'] ?? 10;

            $tags = $this->tagService->trendingTagsByDate($from, $to, $limit);
            return ApiResponse::success(TagResource::collection($tags), "Döneme göre popüler etiketler");
        } catch (ValidationException $e) {
            return ApiResponse::error("Tarih/limit formatı hatalı.", 422, $e->errors());
        } catch (\Throwable $e) {
            return ApiResponse::error("Döneme göre popüler etiketler getirilemedi.", 500);
        }
    }

    public function search(Request $request)
    {
        $q = $request->input('q');
        $tags = $this->tagService->search($q);
        return ApiResponse::success(TagResource::collection($tags), "Arama sonuçları");
    }

    public function destroy(int $id)
    {
        try {
            $this->tagService->delete($id);
            return ApiResponse::success(null, "Etiket silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Etiket bulunamadı", 404);
        } catch (\Throwable $e) {
            return ApiResponse::error("Etiket silinemedi", 500);
        }
    }
}
