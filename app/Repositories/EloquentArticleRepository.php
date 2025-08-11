<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $q = Article::with(['category','tags','author']);

        if (($filters['status'] ?? null) === 'published') $q->published();
        if ($cid = $filters['category_id'] ?? null) $q->where('category_id',$cid);
        if ($lang = $filters['language'] ?? null) $q->where('language',$lang);
        if (!empty($filters['featured'])) $q->featured();

        $q->when($filters['sort'] ?? null, function($qq,$sort){
            foreach (explode(',', $sort) as $s) {
                $dir = str_starts_with($s,'-') ? 'desc':'asc';
                $col = ltrim($s,'-');
                $qq->orderBy($col,$dir);
            }
        }, fn($qq)=>$qq->orderByDesc('published_at')->orderByDesc('id'));

        return $q->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Article
    { return Article::with(['category','tags','author'])->where('slug',$slug)->first(); }

    public function findById(int $id): ?Article
    { return Article::with(['category','tags','author'])->find($id); }

    public function create(array $data): Article
    { return Article::create($data); }

    public function update(Article $article, array $data): Article
    { $article->update($data); return $article; }

    public function delete(Article $article): void
    { $article->delete(); }
}
