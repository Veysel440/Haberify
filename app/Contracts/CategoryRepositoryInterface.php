<?php

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function allActive(): Collection;
    public function findBySlug(string $slug): ?Category;
    public function findById(int $id): ?Category;
    public function create(array $data): Category;
    public function update(Category $category, array $data): Category;
    public function delete(Category $category): void;
}
