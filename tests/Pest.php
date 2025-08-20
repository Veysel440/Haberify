<?php

namespace Tests;


use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class)->in('Feature','Unit');

function userWithPermissions(array $perms): \App\Models\User {
    $u = \App\Models\User::factory()->create();
    foreach ($perms as $p) {
        \Spatie\Permission\Models\Permission::findOrCreate($p, 'web');
    }
    $u->givePermissionTo($perms);
    return $u;
}
