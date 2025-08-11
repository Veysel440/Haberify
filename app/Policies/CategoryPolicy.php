<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    public function manage(User $u): bool { return $u->can('categories.manage'); }
}
