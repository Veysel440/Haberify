<?php

namespace App\Policies;

use App\Models\User;

abstract class BaseResourcePolicy
{
    abstract protected function prefix(): string;

    public function viewAny(?User $user, $model = null): bool { return true; }
    public function view(?User $user, $model = null): bool     { return true; }

    public function create(User $user, $model = null): bool    { return $user->can($this->prefix().'.create'); }
    public function update(User $user, $model = null): bool    { return $user->can($this->prefix().'.update'); }
    public function delete(User $user, $model = null): bool    { return $user->can($this->prefix().'.delete') || $user->can($this->prefix().'.manage'); }
    public function restore(User $user, $model = null): bool   { return $this->delete($user, $model); }
    public function forceDelete(User $user, $model = null): bool { return $this->delete($user, $model); }
}
