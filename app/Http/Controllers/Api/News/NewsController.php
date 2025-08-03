<?php

namespace App\Http\Controllers\Api\News;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\News\NewsResource;
use App\Models\News;
use App\Services\News\NewsService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private NewsService $newsService;

    public function __construct(NewsService $newsService)
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

    public function meta($id)
    {
        $news = News::findOrFail($id);
        return response()->json([
            'title'       => $news->title,
            'description' => $news->excerpt ?? \Str::limit(strip_tags($news->content), 150),
            'image'       => $news->image ? url('storage/' . $news->image) : null,
            'url'         => route('news.show', $news->id)
        ]);
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

    public function search(Request $request)
    {
        $q = $request->get('q');
        $query = News::query();

        if ($q) {
            $query->where(function($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('content', 'like', "%$q%");
            });
        }

        $results = $query->latest()->take(20)->get();

        return ApiResponse::success(NewsResource::collection($results), "Arama sonuçları");
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
