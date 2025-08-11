<?php

namespace App\Repositories;

use App\Contracts\CommentRepositoryInterface;
use App\Models\Comment;
use Illuminate\Support\Collection;

class EloquentCommentRepository implements CommentRepositoryInterface
{
    public function listApprovedForArticle(int $articleId): Collection
    {
        return Comment::with('user:id,name')
            ->where('article_id',$articleId)
            ->where('status','approved')
            ->latest()->get();
    }

    public function findById(int $id): ?Comment
    { return Comment::find($id); }

    public function create(array $data): Comment
    { return Comment::create($data); }

    public function update(Comment $comment, array $data): Comment
    { $comment->update($data); return $comment; }

    public function delete(Comment $comment): void
    { $comment->delete(); }
}
