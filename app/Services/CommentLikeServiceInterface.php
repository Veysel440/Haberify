<?php

namespace App\Services;

interface CommentLikeServiceInterface
{
    public function like(int $commentId, int $userId, bool $isLike): void;
    public function getLikeStats(int $commentId): array;
}
