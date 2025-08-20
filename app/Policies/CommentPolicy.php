<?php

namespace App\Policies;


use App\Models\User;

class CommentPolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'comments'; }

    public function viewAny(?User $user, $model = null): bool { return true; }
    public function view(?User $user, $model = null): bool     { return true; }

    public function create(?User $user = null, $comment = null): bool { return true; }

    public function approve(User $user, $comment): bool { return $user->can('comments.moderate'); }
    public function reject(User $user,  $comment): bool { return $user->can('comments.moderate'); }
    public function update(User $user,  $comment = null): bool { return $user->can('comments.moderate'); }
    public function delete(User $user,  $comment = null): bool { return $user->can('comments.moderate'); }
}
