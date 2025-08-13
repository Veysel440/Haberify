<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TagRepositoryInterface;
use App\DTO\Tag\{CreateTagData, UpdateTagData};
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Support\CacheKeys;

class TagService
{
    public function __construct(private TagRepositoryInterface $repo) {}

    public function all()
    {
        try { return Cache::remember(CacheKeys::tagsActive(), 300, fn()=> $this->repo->allActive()); }
        catch (\Throwable $e) { Log::error('tag.list.fail',['err'=>$e->getMessage()]); throw new ApiException('Etiket listelenemedi',500); }
    }

    public function findBySlug(string $slug)
    {
        try { return Cache::remember(CacheKeys::tagSlug($slug), 300, fn()=> $this->repo->findBySlug($slug)); }
        catch (\Throwable $e) { Log::warning('tag.find.fail',['slug'=>$slug]); throw new ApiException('Etiket bulunamadı',404); }
    }

    public function create(CreateTagData|array $d)
    {
        $arr = $d instanceof CreateTagData ? $d->toArray() : $d;
        try {
            $t = $this->repo->create($arr)->fresh();
            Cache::forget(CacheKeys::tagsActive());
            if ($t->slug) Cache::forget(CacheKeys::tagSlug($t->slug));
            return $t;
        } catch (\Throwable $e) { Log::error('tag.create.fail'); throw new ApiException('Etiket oluşturulamadı',500); }
    }

    public function update(int $id, UpdateTagData|array $d)
    {
        $arr = $d instanceof UpdateTagData ? $d->toArray() : $d;
        $t = $this->repo->findById($id) ?? throw new ApiException('Etiket bulunamadı',404);
        try {
            $old = $t->slug;
            $t = $this->repo->update($t,$arr)->fresh();
            Cache::forget(CacheKeys::tagsActive());
            if ($old) Cache::forget(CacheKeys::tagSlug($old));
            if ($t->slug) Cache::forget(CacheKeys::tagSlug($t->slug));
            return $t;
        } catch (\Throwable $e) { Log::error('tag.update.fail'); throw new ApiException('Etiket güncellenemedi',500); }
    }

    public function delete(int $id): void
    {
        $t = $this->repo->findById($id) ?? throw new ApiException('Etiket bulunamadı',404);
        try {
            $slug = $t->slug;
            $this->repo->delete($t);
            Cache::forget(CacheKeys::tagsActive());
            if ($slug) Cache::forget(CacheKeys::tagSlug($slug));
        } catch (\Throwable $e) { Log::error('tag.delete.fail'); throw new ApiException('Etiket silinemedi',500); }
    }
}
