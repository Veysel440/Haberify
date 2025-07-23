<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentService implements CommentServiceInterface
{
    public function list(int $newsId): LengthAwarePaginator
    {
        return Comment::with('user')
            ->where('news_id', $newsId)
            ->where('status', 'approved')
            ->latest()
            ->paginate(20);
    }

    public function find(int $id): ?Comment
    {
        return Comment::with('user')->findOrFail($id);
    }

    public function create(array $data, int $userId): Comment
    {
        $data['user_id'] = $userId;
        return Comment::create($data);
    }

    public function delete(int $id, int $userId): bool
    {
        $comment = Comment::findOrFail($id);
        if ($comment->user_id !== $userId) {
            throw new \Exception("Yalnızca kendi yorumunuzu silebilirsiniz.", 403);
        }
        return $comment->delete();
    }
}
