<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy extends BaseResourcePolicy
{
    protected function prefix(): string { return 'settings'; }

    public function viewAny(?User $user, $model = null): bool { return false; }
    public function view(?User $user, $model = null): bool     { return (bool) ($user?->can('settings.manage')); }
    public function update(User $user, $model = null): bool    { return $user->can('settings.manage'); }
}
