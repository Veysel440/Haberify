<?php

namespace App\Policies;

use App\Models\User;

class PagePolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'pages'; }

    public function viewAny(?User $user, $model = null): bool { return true; }

    public function view(?User $user, $page = null): bool
    {
        if (!$page) return true;
        return ($page->is_active ?? false) || ($user && $user->can('pages.manage'));
    }
}
