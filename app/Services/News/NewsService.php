<?php

namespace App\Services\News;

use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class NewsService
{
    public function list(): LengthAwarePaginator
    {
        return News::with(['category', 'tags', 'images'])
            ->latest()
            ->paginate(10);
    }

    public function find(int $id): News
    {
        return News::with(['category', 'tags', 'images'])
            ->findOrFail($id);
    }

    public function create(array $data): News
    {
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['excerpt'] = Str::limit(strip_tags($data['content']), 200);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('news-covers', 'public');
        }

        $news = News::create($data);

        if (isset($data['images']) && is_array($data['images'])) {
            $this->addImages($news->id, $data['images']);
        }

        if (isset($data['tags'])) {
            $news->tags()->sync($data['tags']);
        }

        return $news->load(['category', 'tags', 'images']);
    }

    public function update(int $id, array $data): News
    {
        $news = News::findOrFail($id);

        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $news->id);
        }
        if (isset($data['content'])) {
            $data['excerpt'] = Str::limit(strip_tags($data['content']), 200);
        }

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $data['image']->store('news-covers', 'public');
        }

        if (isset($data['scheduled_at'])) {
            $data['status'] = 'scheduled';
        }

        $news->update($data);

        if (isset($data['tags'])) {
            $news->tags()->sync($data['tags']);
        }

        return $news->load(['category', 'tags', 'images']);
    }

    private function generateUniqueSlug(string $title, $exceptId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $i = 2;

        while (
        News::where('slug', $slug)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function addImages(int $newsId, array $images): void
    {
        $news = News::findOrFail($newsId);

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store('news-gallery', 'public');
                $news->images()->create(['image' => $path]);
            }
        }
    }

    public function delete(int $id): bool
    {
        $news = News::with('images', 'tags')->findOrFail($id);

        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }

        foreach ($news->images as $gallery) {
            if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
                Storage::disk('public')->delete($gallery->image);
            }
            $gallery->delete();
        }

        $news->tags()->detach();

        return $news->delete();
    }
}
