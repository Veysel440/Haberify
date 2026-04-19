<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Without the TestCase wiring, Pest tests extend PHPUnit's bare TestCase
 * and never boot the Laravel framework — facades, container, config and
 * the full FormRequest pipeline all come from the `Tests\TestCase` base
 * class.
 */
uses(TestCase::class, RefreshDatabase::class)->in('Feature');
uses(TestCase::class)->in('Unit');

/**
 * Global test helper — kept in the root namespace so test files can call
 * it without a `use function` import. Pest's own globals (`it`, `expect`,
 * ...) live at the root namespace for the same reason.
 *
 * @param list<string> $perms
 */
function userWithPermissions(array $perms): \App\Models\User
{
    $u = \App\Models\User::factory()->create();
    foreach ($perms as $p) {
        \Spatie\Permission\Models\Permission::findOrCreate($p, 'web');
    }
    $u->givePermissionTo($perms);

    return $u;
}
