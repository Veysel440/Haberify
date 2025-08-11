<?php

namespace App\Contracts;

use App\Models\Tag;
use Illuminate\Support\Collection;

interface TagRepositoryInterface
{
    public function allActive(): Collection;
    public function findBySlug(string $slug): ?Tag;
    public function findById(int $id): ?Tag;
    public function create(array $data): Tag;
    public function update(Tag $tag, array $data): Tag;
    public function delete(Tag $tag): void;
}
