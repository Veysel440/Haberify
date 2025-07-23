<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Services\CommentServiceInterface;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CommentController extends Controller
{
    private CommentServiceInterface $commentService;

    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService = $commentService;
    }

    public function index(Request $request)
    {
        try {
            $newsId = $request->query('news_id');
            if (!$newsId) {
                return ApiResponse::error("Haber ID zorunlu.", 422);
            }
            $comments = $this->commentService->list($newsId);
            return ApiResponse::success(CommentResource::collection($comments), "Yorumlar listelendi");
        } catch (Exception $e) {
            return ApiResponse::error("Yorumlar listelenemedi.", 500);
        }
    }


    public function store(StoreCommentRequest $request)
    {
        try {
            $userId = $request->user()->id;
            $comment = $this->commentService->create($request->validated(), $userId);
            return ApiResponse::success(new CommentResource($comment), "Yorum gönderildi, onay bekliyor.", 201);
        } catch (Exception $e) {
            return ApiResponse::error("Yorum eklenemedi.", 500);
        }
    }


    public function show($id)
    {
        try {
            $comment = $this->commentService->find($id);
            return ApiResponse::success(new CommentResource($comment), "Yorum detayı");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Yorum getirilemedi.", 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $userId = $request->user()->id;
            $this->commentService->delete($id, $userId);
            return ApiResponse::success(null, "Yorum silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
