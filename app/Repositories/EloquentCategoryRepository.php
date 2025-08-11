<?php

namespace App\Repositories;

use App\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function allActive(): Collection
    { return Category::where('is_active',true)->orderBy('name')->get(); }

    public function findBySlug(string $slug): ?Category
    { return Category::where('slug',$slug)->first(); }

    public function findById(int $id): ?Category
    { return Category::find($id); }

    public function create(array $data): Category
    { return Category::create($data); }

    public function update(Category $category, array $data): Category
    { $category->update($data); return $category; }

    public function delete(Category $category): void
    { $category->delete(); }
}
