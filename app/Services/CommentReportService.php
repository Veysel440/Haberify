<?php

namespace App\Services;

use App\Models\CommentReport;

class CommentReportService implements CommentReportServiceInterface
{
    public function report(int $commentId, int $userId, ?string $reason): void
    {
        CommentReport::firstOrCreate(
            ['comment_id' => $commentId, 'user_id' => $userId],
            ['reason' => $reason]
        );
    }

    public function getReports($filters = [])
    {
        $query = CommentReport::with('comment', 'user')->latest();
        if (isset($filters['comment_id'])) {
            $query->where('comment_id', $filters['comment_id']);
        }
        return $query->get();
    }

    public function removeReport(int $reportId): void
    {
        CommentReport::where('id', $reportId)->delete();
    }
}
