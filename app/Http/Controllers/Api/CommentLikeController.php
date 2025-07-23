<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommentLikeServiceInterface;
use App\Helpers\ApiResponse;


class CommentLikeController extends Controller
{
    private CommentLikeServiceInterface $commentLikeService;

    public function __construct(CommentLikeServiceInterface $commentLikeService)
    {
        $this->commentLikeService = $commentLikeService;
    }

    public function like(Request $request, $commentId)
    {
        $request->validate([
            'is_like' => 'required|boolean',
        ]);
        $this->commentLikeService->like($commentId, $request->user()->id, $request->input('is_like'));
        return ApiResponse::success(null, 'Oy verildi');
    }

    public function stats($commentId)
    {
        $stats = $this->commentLikeService->getLikeStats($commentId);
        return ApiResponse::success($stats, 'Yorum beğeni/oy istatistikleri');
    }
}
