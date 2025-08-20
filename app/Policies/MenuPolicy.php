<?php

namespace App\Policies;


use App\Models\User;

class MenuPolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'menus'; }

    public function update(User $user, $menu = null): bool
    {
        return $user->can('menus.edit');
    }
}
