<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TagService implements TagServiceInterface
{
    public function list(): LengthAwarePaginator
    {
        return Tag::latest()->paginate(20);
    }

    public function find(int $id): ?Tag
    {
        return Tag::findOrFail($id);
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(int $id, array $data): ?Tag
    {
        $tag = Tag::findOrFail($id);
        $tag->update($data);
        return $tag;
    }

    public function delete(int $id): bool
    {
        $tag = Tag::findOrFail($id);
        return $tag->delete();
    }
}
