<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;

interface AdminServiceInterface
{
    public function approveComment(int $commentId): Comment;
    public function rejectComment(int $commentId): Comment;
    public function makeFeatured(int $newsId): News;
    public function unpublishNews(int $newsId): News;
    public function listUsers(): \Illuminate\Database\Eloquent\Collection;
    public function makeAdmin(int $userId): User;
}
