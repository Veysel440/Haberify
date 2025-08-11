<?php

namespace App\Contracts;

use App\Models\Comment;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function listApprovedForArticle(int $articleId): Collection;
    public function findById(int $id): ?Comment;
    public function create(array $data): Comment;
    public function update(Comment $comment, array $data): Comment;
    public function delete(Comment $comment): void;
}
