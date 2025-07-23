<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Category;

interface CategoryServiceInterface
{
    public function list(): LengthAwarePaginator;
    public function find(int $id): ?Category;
    public function create(array $data): Category;
    public function update(int $id, array $data): ?Category;
    public function delete(int $id): bool;
}
