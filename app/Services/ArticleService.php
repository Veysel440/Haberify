<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\ArticlePublished;
class ArticleService
{
    public function __construct(private ArticleRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage = 15)
    { return $this->repo->paginate($filters, $perPage); }

    public function findBySlug(string $slug): ?Article
    { return $this->repo->findBySlug($slug); }

    public function create(array $data): Article {
        return \DB::transaction(function() use ($data){
            $data['reading_time'] = $data['reading_time'] ?? $this->estimate($data['body'] ?? '');
            $a = $this->repo->create($data);
            if (!empty($data['tag_ids'])) $a->tags()->sync($data['tag_ids']);
            if ($a->status === 'published') $a->searchable();
            return $a->load(['category','tags','author']);
        });
    }

    public function update(int $id, array $data): Article {
        $a = $this->repo->findById($id) ?? abort(404,'Article not found');
        return \DB::transaction(function() use ($a,$data){
            $a = $this->repo->update($a,$data);
            if (array_key_exists('tag_ids',$data)) $a->tags()->sync($data['tag_ids'] ?? []);
            if ($a->status === 'published') $a->searchable(); else $a->unsearchable();
            return $a->load(['category','tags','author']);
        });
    }

    public function publish(int $id): Article {
        $a = $this->repo->findById($id) ?? abort(404);
        $a = $this->repo->update($a, ['status'=>'published','published_at'=>now()]);
        $a->searchable();
        \Cache::forget('rss:latest'); \Cache::forget('sitemap:xml');
        return $a->load(['category','tags','author']);
    }

    public function delete(int $id): void
    {
        $a = $this->repo->findById($id) ?? abort(404);
        $this->repo->delete($a);
    }

    private function estimate(string $text): int
    {
        $words = str_word_count(strip_tags($text));
        return max(1, (int)ceil($words/200));
    }
}
