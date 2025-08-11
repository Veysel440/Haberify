<?php

namespace App\Services;

use App\Contracts\TagRepositoryInterface;

class TagService
{
    public function __construct(private TagRepositoryInterface $repo) {}

    public function all(){ return $this->repo->allActive(); }
    public function findBySlug(string $slug){ return $this->repo->findBySlug($slug); }
    public function create(array $d){ return $this->repo->create($d)->fresh(); }
    public function update(int $id, array $d){ $t = $this->repo->findById($id) ?? abort(404); return $this->repo->update($t,$d)->fresh(); }
    public function delete(int $id){ $t = $this->repo->findById($id) ?? abort(404); $this->repo->delete($t); }
}
