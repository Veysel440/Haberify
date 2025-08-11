<?php

namespace App\Repositories;

use App\Contracts\TagRepositoryInterface;
use App\Models\Tag;
use Illuminate\Support\Collection;

class EloquentTagRepository implements TagRepositoryInterface
{
    public function allActive(): Collection
    { return Tag::where('is_active',true)->orderBy('name')->get(); }

    public function findBySlug(string $slug): ?Tag
    { return Tag::where('slug',$slug)->first(); }

    public function findById(int $id): ?Tag
    { return Tag::find($id); }

    public function create(array $data): Tag
    { return Tag::create($data); }

    public function update(Tag $tag, array $data): Tag
    { $tag->update($data); return $tag; }

    public function delete(Tag $tag): void
    { $tag->delete(); }
}
