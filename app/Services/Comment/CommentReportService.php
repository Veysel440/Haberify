<?php

namespace App\Services\Comment;

use App\Models\CommentReport;

class CommentReportService
{
    public function report(int $commentId, int $userId, ?string $reason): void
    {
        CommentReport::updateOrCreate(
            ['comment_id' => $commentId, 'user_id' => $userId],
            ['reason' => $reason]
        );
    }

    public function getReports(array $filters = [])
    {
        $query = CommentReport::with(['comment', 'user'])->latest();

        if (!empty($filters['comment_id'])) {
            $query->where('comment_id', $filters['comment_id']);
        }
        return $query->get();
    }

    public function removeReport(int $reportId): void
    {
        CommentReport::where('id', $reportId)->delete();
    }
}
