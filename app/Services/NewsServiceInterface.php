<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\News;

interface NewsServiceInterface
{
    public function list(): LengthAwarePaginator;
    public function find(int $id): ?News;
    public function create(array $data): News;
    public function update(int $id, array $data): ?News;
    public function delete(int $id): bool;
}
