<?php

namespace App\Services;

interface CommentReportServiceInterface
{
    public function report(int $commentId, int $userId, ?string $reason): void;
    public function getReports($filters = []);
    public function removeReport(int $reportId): void;
}
