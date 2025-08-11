<?php

namespace App\Policies;

use App\Models\{User, Comment};

class CommentPolicy
{
    public function moderate(User $u): bool { return $u->can('comments.moderate'); }
    public function delete(User $u, Comment $c): bool
    { return $u->can('comments.moderate') || $u->id === $c->user_id; }
}
