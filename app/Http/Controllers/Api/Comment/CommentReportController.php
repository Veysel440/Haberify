<?php

namespace App\Http\Controllers\Api\Comment;


use App\Helpers\ApiResponse;
use App\Http\Requests\Comment\ReportCommentRequest;
use App\Services\Comment\CommentReportService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\Controller;

class CommentReportController extends Controller
{
    private CommentReportService $commentReportService;

    public function __construct(CommentReportService $commentReportService)
    {
        $this->commentReportService = $commentReportService;
    }

    public function report($commentId, ReportCommentRequest $request)
    {
        try {
            $this->commentReportService->report(
                (int)$commentId,
                $request->user()->id,
                $request->input('reason')
            );
            return ApiResponse::success(null, "Yorum bildiriminiz alınmıştır.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            \Log::error($e);
            return ApiResponse::error("Bildirim kaydedilemedi.", 500);
        }
    }

    public function index()
    {
        try {
            $reports = $this->commentReportService->getReports();
            return ApiResponse::success($reports, "Tüm raporlar");
        } catch (Exception $e) {
            \Log::error($e);
            return ApiResponse::error("Raporlar getirilemedi.", 500);
        }
    }

    public function destroy($reportId)
    {
        try {
            $this->commentReportService->removeReport((int)$reportId);
            return ApiResponse::success(null, "Bildirim kaldırıldı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Bildirim bulunamadı.", 404);
        } catch (Exception $e) {
            \Log::error($e);
            return ApiResponse::error("Bildirim kaldırılamadı.", 500);
        }
    }
}
