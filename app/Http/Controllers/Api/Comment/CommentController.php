<?php

namespace App\Http\Controllers\Api\Comment;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment;
use App\Services\Comment\CommentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
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

    public function replies($commentId)
    {
        try {
            $comment = Comment::with('replies.user')->findOrFail($commentId);
            return ApiResponse::success(CommentResource::collection($comment->replies), "Yanıtlar listelendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Yanıtlar getirilemedi.", 500);
        }
    }

    public function update(UpdateCommentRequest $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $this->authorize('update', $comment);
            $updated = $this->commentService->update($id, $request->validated(), $request->user()->id);
            return ApiResponse::success(new CommentResource($updated), "Yorum güncellendi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (AuthorizationException $e) {
            return ApiResponse::error("Bu işlemi yapmaya yetkiniz yok.", 403);
        } catch (Exception $e) {
            return ApiResponse::error("Yorum güncellenemedi.", 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $this->authorize('delete', $comment);
            $this->commentService->delete($id, $request->user()->id);
            return ApiResponse::success(null, "Yorum silindi");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (AuthorizationException $e) {
            return ApiResponse::error("Bu işlemi yapmaya yetkiniz yok.", 403);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function like(Request $request, $id)
    {
        $request->validate(['is_like' => 'required|boolean']);
        $userId = $request->user()->id;
        $this->commentService->like($id, $userId, $request->input('is_like'));
        return ApiResponse::success(null, 'Oy verildi');
    }

    public function report(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $userId = $request->user()->id;
        $this->commentService->report($id, $userId, $request->input('reason'));
        return ApiResponse::success(null, "Yorum bildiriminiz alınmıştır.");
    }
}
