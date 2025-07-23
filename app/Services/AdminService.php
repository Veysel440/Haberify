<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminService implements AdminServiceInterface
{
    public function approveComment(int $commentId): Comment
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'approved';
        $comment->save();
        return $comment;
    }

    public function rejectComment(int $commentId): Comment
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'rejected';
        $comment->save();
        return $comment;
    }

    public function makeFeatured(int $newsId): News
    {
        $news = News::findOrFail($newsId);
        $news->is_featured = true;
        $news->save();
        return $news;
    }

    public function unpublishNews(int $newsId): News
    {
        $news = News::findOrFail($newsId);
        $news->status = 'draft';
        $news->save();
        return $news;
    }

    public function listUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderByDesc('created_at')->get();
    }

    public function makeAdmin(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->role = 'admin';
        $user->save();
        return $user;
    }
}
