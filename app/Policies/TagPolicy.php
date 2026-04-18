<?php

namespace App\Policies;

use App\Models\User;

class TagPolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'tags'; }

    public function viewAny(?User $user, $model = null): bool { return true; }
    public function view(?User $user, $model = null): bool     { return true; }
}
