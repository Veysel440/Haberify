<?php

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repo) {}

    public function all(){ return $this->repo->allActive(); }
    public function findBySlug(string $slug){ return $this->repo->findBySlug($slug); }
    public function create(array $d){ return $this->repo->create($d)->fresh(); }
    public function update(int $id, array $d){ $c = $this->repo->findById($id) ?? abort(404); return $this->repo->update($c,$d)->fresh(); }
    public function delete(int $id){ $c = $this->repo->findById($id) ?? abort(404); $this->repo->delete($c); }
}
