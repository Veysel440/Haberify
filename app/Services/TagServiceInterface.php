<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Tag;

interface TagServiceInterface
{
    public function list(): LengthAwarePaginator;
    public function find(int $id): ?Tag;
    public function create(array $data): Tag;
    public function update(int $id, array $data): ?Tag;
    public function delete(int $id): bool;
}
