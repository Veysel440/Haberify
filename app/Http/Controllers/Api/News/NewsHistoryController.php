<?php

namespace App\Http\Controllers\Api\News;

use App\Http\Controllers\Controller;
use App\Http\Resources\News\NewsHistoryResource;
use App\Models\News;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class NewsHistoryController extends Controller
{
    public function index($newsId): JsonResponse
    {
        try {
            $news = News::findOrFail($newsId);
            $histories = $news->histories()->with('editor')->latest()->get();

            return response()->json([
                'success' => true,
                'data'    => NewsHistoryResource::collection($histories),
                'message' => 'Haber geçmişi başarıyla listelendi.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Haber bulunamadı.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçmiş kayıtlar getirilemedi.',
            ], 500);
        }
    }
}
