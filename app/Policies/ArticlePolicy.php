<?php

namespace App\Policies;

use App\Models\User;

class ArticlePolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'articles'; }

    public function publish(User $user, $article = null): bool
    {
        return $user->can('articles.publish');
    }

    public function viewAny(?User $user, $model = null): bool { return true; }

    public function view(?User $user, $article = null): bool
    {
        if (!$article) return true;
        if (($article->status ?? null) === 'published') return true;
        if (!$user) return false;

        return $user->id === ($article->author_id ?? null)
            || $user->hasAnyRole(['editor','admin'])
            || $user->can('articles.update');
    }

    public function update(User $user, $article = null): bool
    {
        if (!$user->can('articles.update')) return false;
        if (!$article) return true;

        return $user->id === ($article->author_id ?? null)
            || $user->hasAnyRole(['editor','admin']);
    }

    public function delete(User $user, $article = null): bool
    {
        return $user->can('articles.delete') || $user->hasRole('admin');
    }
}
