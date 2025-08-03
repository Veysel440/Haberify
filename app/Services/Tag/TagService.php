<?php

namespace App\Services\Tag;

use App\Models\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TagService
{
    public function list(): LengthAwarePaginator
    {
        return Tag::latest()->paginate(20);
    }

    public function find(int $id): Tag
    {
        return Tag::findOrFail($id);
    }

    public function findBySlug(string $slug): Tag
    {
        return Tag::where('slug', $slug)->firstOrFail();
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(int $id, array $data): Tag
    {
        $tag = Tag::findOrFail($id);
        $tag->update($data);
        return $tag;
    }

    public function delete(int $id): bool
    {
        $tag = Tag::findOrFail($id);
        $tag->news()->detach();
        return $tag->delete();
    }

    public function trendingTags(int $limit = 10): Collection
    {
        return Tag::withCount('news')
            ->orderByDesc('news_count')
            ->take($limit)
            ->get();
    }

    public function trendingTagsByDate(string $from, string $to, int $limit = 10): Collection
    {
        return Tag::whereHas('news', function($query) use ($from, $to) {
            $query->whereBetween('news.created_at', [$from, $to]);
        })
            ->withCount(['news' => function($query) use ($from, $to) {
                $query->whereBetween('news.created_at', [$from, $to]);
            }])
            ->orderByDesc('news_count')
            ->take($limit)
            ->get();
    }

    public function search(?string $q): Collection
    {
        if (!$q) {
            return collect();
        }
        return Tag::where('name', 'like', "%{$q}%")
            ->orWhere('slug', 'like', "%{$q}%")
            ->take(20)
            ->get();
    }
}
