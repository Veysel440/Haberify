<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;
use App\DTO\Category\{CreateCategoryData, UpdateCategoryData};
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Support\CacheKeys;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repo) {}

    public function all()
    {
        try {
            return Cache::remember(CacheKeys::categoriesActive(), 300, fn()=> $this->repo->allActive());
        } catch (\Throwable $e) {
            Log::error('category.list.fail', ['err'=>$e->getMessage()]);
            throw new ApiException('Kategori listelenemedi', 500);
        }
    }

    public function findBySlug(string $slug)
    {
        try {
            return Cache::remember(CacheKeys::categorySlug($slug), 300, fn()=> $this->repo->findBySlug($slug));
        } catch (\Throwable $e) {
            Log::warning('category.find.fail', ['slug'=>$slug,'err'=>$e->getMessage()]);
            throw new ApiException('Kategori bulunamadı', 404);
        }
    }

    public function create(CreateCategoryData|array $d)
    {
        $arr = $d instanceof CreateCategoryData ? $d->toArray() : $d;
        try {
            $c = $this->repo->create($arr)->fresh();
            Cache::forget(CacheKeys::categoriesActive());
            if ($c->slug) Cache::forget(CacheKeys::categorySlug($c->slug));
            return $c;
        } catch (\Throwable $e) {
            Log::error('category.create.fail', ['payload'=>$arr,'err'=>$e->getMessage()]);
            throw new ApiException('Kategori oluşturulamadı', 500);
        }
    }

    public function update(int $id, UpdateCategoryData|array $d)
    {
        $arr = $d instanceof UpdateCategoryData ? $d->toArray() : $d;
        $c = $this->repo->findById($id) ?? throw new ApiException('Kategori bulunamadı', 404);
        try {
            $old = $c->slug;
            $c = $this->repo->update($c, $arr)->fresh();
            Cache::forget(CacheKeys::categoriesActive());
            if ($old) Cache::forget(CacheKeys::categorySlug($old));
            if ($c->slug) Cache::forget(CacheKeys::categorySlug($c->slug));
            return $c;
        } catch (\Throwable $e) {
            Log::error('category.update.fail', ['id'=>$id,'err'=>$e->getMessage()]);
            throw new ApiException('Kategori güncellenemedi', 500);
        }
    }

    public function delete(int $id): void
    {
        $c = $this->repo->findById($id) ?? throw new ApiException('Kategori bulunamadı', 404);
        try {
            $slug = $c->slug;
            $this->repo->delete($c);
            Cache::forget(CacheKeys::categoriesActive());
            if ($slug) Cache::forget(CacheKeys::categorySlug($slug));
        } catch (\Throwable $e) {
            Log::error('category.delete.fail', ['id'=>$id,'err'=>$e->getMessage()]);
            throw new ApiException('Kategori silinemedi', 500);
        }
    }
}
