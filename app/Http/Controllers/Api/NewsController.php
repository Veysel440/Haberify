<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Services\NewsServiceInterface;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class NewsController extends Controller
{
    private NewsServiceInterface $newsService;

    public function __construct(NewsServiceInterface $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index()
    {
        try {
            $news = $this->newsService->list();
            return ApiResponse::success(NewsResource::collection($news), "Haberler listelendi");
        } catch (Exception $e) {
            return ApiResponse::error("Haberler listelenirken hata oluştu.", 500);
        }
    }

    public function show($id)
    {
        try {
            $news = $this->newsService->find($id);
            $news->increment('views');
            return ApiResponse::success(new NewsResource($news), "Haber detay");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Haber bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Beklenmeyen bir hata oluştu.", 500);
        }
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('images')) {
                $data['images'] = $request->file('images');
            }

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $news = $this->newsService->create($data);

            return ApiResponse::success(new NewsResource($news), "Haber eklendi", 201);

        } catch (\Exception $e) {
            return ApiResponse::error("Haber eklenirken hata oluştu.", 500);
        }
    }

    public function update(UpdateNewsRequest $request, $id)
    {
        try {
            $news = $this->newsService->update($id, $request->validated());
            return ApiResponse::success(new NewsResource($news), "Haber güncellendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Haber bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Haber güncellenirken hata oluştu.", 500);
        }
    }
    public function uploadGallery(Request $request, $newsId)
    {
        $request->validate([
            'images'   => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);
        $this->newsService->addImages($newsId, $request->file('images'));
        return ApiResponse::success(null, 'Galeri görselleri başarıyla yüklendi.');
    }

    public function destroy($id)
    {
        try {
            $this->newsService->delete($id);
            return ApiResponse::success(null, "Haber silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Haber bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Haber silinirken hata oluştu.", 500);
        }
    }
}
