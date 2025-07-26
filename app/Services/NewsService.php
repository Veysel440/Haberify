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
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['excerpt'] = \Str::limit(strip_tags($data['content']), 200);

        $news = News::create($data);


        if (isset($data['images']) && is_array($data['images'])) {
            $this->addImages($news->id, $data['images']);
        }

        if (isset($data['tags'])) {
            $news->tags()->sync($data['tags']);
        }

        return $news->load('category', 'tags', 'images');
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


        if (isset($data['scheduled_at'])) {
            $data['status'] = 'scheduled';
        }

        $news->update($data);

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
    public function addImages($newsId, array $images)
    {
        $news = News::findOrFail($newsId);

        foreach ($images as $image) {
            $path = $image->store('news-gallery', 'public');
            $news->images()->create(['image' => $path]);
        }
    }

    public function delete(int $id): bool
    {
        $news = News::findOrFail($id);
        return $news->delete();
    }
}
