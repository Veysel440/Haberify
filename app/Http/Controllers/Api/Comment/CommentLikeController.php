<?php

namespace App\Http\Controllers\Api\Comment;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Comment\CommentLikeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;

class CommentLikeController extends Controller
{
    private CommentLikeService $commentLikeService;

    public function __construct(CommentLikeService $commentLikeService)
    {
        $this->commentLikeService = $commentLikeService;
    }

    public function like(Request $request, $commentId)
    {
        try {
            $request->validate(['is_like' => 'required|boolean']);

            $comment = \App\Models\Comment::findOrFail($commentId);

            $this->commentLikeService->like(
                $commentId,
                $request->user()->id,
                $request->boolean('is_like')
            );

            return ApiResponse::success(null, 'Oy verildi');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Yorum bulunamadı.', 404);
        } catch (Exception $e) {
            return ApiResponse::error('Oy verilemedi: ' . $e->getMessage(), 500);
        }
    }

    public function stats($commentId)
    {
        try {
            $comment = \App\Models\Comment::findOrFail($commentId);

            $stats = $this->commentLikeService->getLikeStats($commentId);
            return ApiResponse::success($stats, 'Yorum beğeni/oy istatistikleri');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error('Yorum bulunamadı.', 404);
        } catch (Exception $e) {
            return ApiResponse::error('İstatistik getirilemedi.', 500);
        }
    }
}
