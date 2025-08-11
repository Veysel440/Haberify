<?php

namespace App\Policies;

use App\Models\{User, Article};

class ArticlePolicy
{
    public function viewAny(User $u): bool { return true; }
    public function view(User $u = null, Article $a): bool { return true; }
    public function create(User $u): bool { return $u->can('articles.create'); }
    public function update(User $u, Article $a): bool
    { return $u->can('articles.update') && ($u->id === $a->author_id || $u->hasAnyRole(['editor','admin'])); }
    public function publish(User $u, Article $a): bool { return $u->can('articles.publish'); }
    public function delete(User $u, Article $a): bool
    { return $u->can('articles.delete') || $u->hasRole('admin'); }
}
