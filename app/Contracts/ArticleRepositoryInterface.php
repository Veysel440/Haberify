<?php

namespace App\Contracts;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function paginate(array $filters, int $perPage): LengthAwarePaginator;
    public function findBySlug(string $slug): ?Article;
    public function findById(int $id): ?Article;
    public function create(array $data): Article;
    public function update(Article $article, array $data): Article;
    public function delete(Article $article): void;
}
