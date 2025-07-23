<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Comment;

interface CommentServiceInterface
{
    public function list(int $newsId): LengthAwarePaginator;
    public function find(int $id): ?Comment;
    public function create(array $data, int $userId): Comment;
    public function delete(int $id, int $userId): bool;
}
