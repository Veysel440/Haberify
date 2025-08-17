<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\DTO\Article\{CreateArticleData, UpdateArticleData};
use App\Exceptions\ApiException;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class ArticleService
{
    public function __construct(private ArticleRepositoryInterface $repo) {}

    public function list(array $filters, int $perPage)
    { return $this->repo->paginate($filters, $perPage); }

    public function findBySlug(string $slug): ?Article
    { return $this->repo->findBySlug($slug); }

    public function create(CreateArticleData|array $data): Article
    {
        $arr = $data instanceof CreateArticleData ? $data->toArray() : $data;
        try {
            return DB::transaction(function () use ($arr) {
                $arr['reading_time'] = $arr['reading_time'] ?? estimate_minutes($arr['body'] ?? '');
                /** @var Article $a */
                $a = $this->repo->create($arr);
                if (!empty($arr['tag_ids'])) $a->tags()->sync($arr['tag_ids']);
                if ($a->status === 'published') $a->searchable();
                return $a->load(['category','tags','author']);
            });
        } catch (\Throwable $e) {
            Log::error('article.create.fail', ['err'=>$e->getMessage()]);
            throw new ApiException('Makale oluşturulamadı', 500);
        }
    }

    public function update(int $id, UpdateArticleData|array $data): Article
    {
        $arr = $data instanceof UpdateArticleData ? $data->toArray() : $data;
        $a = $this->repo->findById($id) ?? throw new ApiException('Makale bulunamadı',404);
        try {
            return DB::transaction(function () use ($a,$arr) {
                $a = $this->repo->update($a, $arr);
                if (array_key_exists('tag_ids',$arr)) $a->tags()->sync($arr['tag_ids'] ?? []);
                if ($a->status === 'published') $a->searchable(); else $a->unsearchable();
                $this->revalidate(['articles', "article:{$a->slug}"]);
                return $a->load(['category','tags','author']);
            });
        } catch (\Throwable $e) {
            Log::warning('article.update.fail', ['id'=>$a->id,'err'=>$e->getMessage()]);
            throw new ApiException('Makale güncellenemedi', 500);
        }
    }

    public function publish(int $id): Article
    {
        $a = $this->repo->findById($id) ?? throw new ApiException('Makale bulunamadı',404);
        try {
            $a = $this->repo->update($a, ['status'=>'published','published_at'=>now()]);
            $a->searchable(); \Cache::forget('rss:latest'); \Cache::forget('sitemap:xml');
            $this->revalidate(['articles', "article:{$a->slug}", "category:{$a->category?->slug}", ...$a->tags->map(fn($t)=>"tag:{$t->slug}")->all()]);
            return $a->load(['category','tags','author']);
        } catch (\Throwable $e) {
            Log::error('article.publish.fail', ['id'=>$id,'err'=>$e->getMessage()]);
            throw new ApiException('Yayınlama başarısız', 500);
        }

    }

    private function revalidate(array $tags): void
    {
        try {
            Http::timeout(3)->post(config('app.front_revalidate_url'), [
                'secret' => config('app.front_revalidate_secret'),
                'tags'   => array_values(array_unique($tags)),
            ]);
        } catch (\Throwable $e) {
            \Log::info('front.revalidate.fail', ['err'=>$e->getMessage()]);
        }
    }

    public function delete(int $id): void
    {
        $a = $this->repo->findById($id) ?? throw new ApiException('Makale bulunamadı',404);
        $this->repo->delete($a);
    }

    public function estimate(string $html): int { return estimate_minutes($html); }
}
