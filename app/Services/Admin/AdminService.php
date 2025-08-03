<?php

namespace App\Services\Admin;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Events\CommentApproved;
use App\Events\CommentRejected;
use App\Events\NewsFeatured;
use App\Events\NewsUnpublished;
use App\Notifications\CommentApprovedNotification;
use App\Notifications\CommentRejectedNotification;
use App\Notifications\NewsFeaturedNotification;
use App\Notifications\NewsUnpublishedNotification;

class AdminService
{
    public function approveComment(int $commentId): Comment
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'approved';
        $comment->save();

        event(new CommentApproved($comment));
        $comment->user?->notify(new CommentApprovedNotification($comment));
        return $comment;
    }

    public function rejectComment(int $commentId): Comment
    {
        $comment = Comment::findOrFail($commentId);
        $comment->status = 'rejected';
        $comment->save();

        event(new CommentRejected($comment));
        $comment->user?->notify(new CommentRejectedNotification($comment));
        return $comment;
    }

    public function makeFeatured(int $newsId): News
    {
        $news = News::findOrFail($newsId);
        $news->is_featured = true;
        $news->save();

        event(new NewsFeatured($news));

        $news->user?->notify(new NewsFeaturedNotification($news));

        return $news;
    }

    public function unpublishNews(int $newsId): News
    {
        $news = News::findOrFail($newsId);
        $news->status = 'draft';
        $news->save();

        event(new NewsUnpublished($news));
        $news->user?->notify(new NewsUnpublishedNotification($news));

        return $news;
    }

    public function listUsers(): Collection
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

    public function suspendUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->status = 'banned';
        $user->save();

        return $user;
    }

    public function activateUser(int $userId): User
    {
        $user = User::findOrFail($userId);
        $user->status = 'active';
        $user->save();

        return $user;
    }
}
