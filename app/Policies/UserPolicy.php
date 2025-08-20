<?php

namespace App\Policies;


use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user, $model = null): bool { return $user->can('users.manage'); }
    public function view(User $user, $target = null): bool { return $user->can('users.manage') || $user->id === ($target->id ?? null); }
    public function assignRole(User $user, $target = null): bool { return $user->can('users.manage'); }
}
