<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'categories'; }

    public function viewAny(?User $user, $model = null): bool { return true; }
    public function view(?User $user, $model = null): bool     { return true; }
}
