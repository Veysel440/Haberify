<?php

namespace App\Policies;


use App\Models\User;
use App\Models\Tag;

class TagPolicy
{
    public function manage(User $u): bool { return $u->can('tags.manage'); }
}
