<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportCommentRequest;
use App\Services\CommentReportServiceInterface;
use App\Helpers\ApiResponse;

class CommentReportController extends Controller
{
    private CommentReportServiceInterface $commentReportService;

    public function __construct(CommentReportServiceInterface $commentReportService)
    {
        $this->commentReportService = $commentReportService;
    }

    public function report($commentId, ReportCommentRequest $request)
    {
        $this->commentReportService->report($commentId, $request->user()->id, $request->input('reason'));
        return ApiResponse::success(null, "Yorum bildiriminiz alınmıştır.");
    }


    public function index()
    {
        $reports = $this->commentReportService->getReports();
        return ApiResponse::success($reports, "Tüm raporlar");
    }

    public function destroy($reportId)
    {
        $this->commentReportService->removeReport($reportId);
        return ApiResponse::success(null, "Bildirim kaldırıldı.");
    }
}
