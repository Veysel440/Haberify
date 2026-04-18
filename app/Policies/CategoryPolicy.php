<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CategoryPolicy extends BaseResourcePolicy
{
    protected function prefix(): string
    {
        return 'categories';
    }

    public function viewAny(?User $user, $model = null): bool
    {
        return true;
    }

    public function view(?User $user, $model = null): bool
    {
        return true;
    }
}
