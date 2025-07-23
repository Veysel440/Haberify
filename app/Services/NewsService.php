<?php

namespace App\Services;

use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsService implements NewsServiceInterface
{
    public function list(): LengthAwarePaginator
    {
        return News::with('category', 'tags')->latest()->paginate(10);
    }

    public function find(int $id): ?News
    {
        return News::with('category', 'tags')->findOrFail($id);
    }

    public function create(array $data): News
    {
        // 1. Slug üret (benzersiz)
        $slug = $this->generateUniqueSlug($data['title']);
        $data['slug'] = $slug;

        // 2. Excerpt üret
        $data['excerpt'] = Str::limit(strip_tags($data['content']), 200);

        // 3. Oluştur ve tag sync vs.
        $news = News::create($data);
        if (isset($data['tags'])) {
            $news->tags()->sync($data['tags']);
        }
        return $news->load('category', 'tags');
    }

    private function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $original = $slug;
        $i = 2;
        while (News::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    public function update(int $id, array $data): ?News
    {
        $news = News::findOrFail($id);
        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }
        if (isset($data['content'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 200);
        }
        $news->update($data);
        if (isset($data['tags'])) {
            $news->tags()->sync($data['tags']);
        }
        return $news->load('category', 'tags');
    }

    public function delete(int $id): bool
    {
        $news = News::findOrFail($id);
        return $news->delete();
    }
}
