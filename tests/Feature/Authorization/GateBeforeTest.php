<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Authorisation wiring regression tests.
 *
 * Previously `AuthServiceProvider::boot` registered:
 *
 *   Gate::define('users.manage', fn ($user) => $user->can('users.manage'));
 *
 * which, for any user who does NOT hold the ability, would recurse
 * (Gate::check → closure → $user->can → Gate::check → ...) until the
 * PHP stack overflowed. `Gate::before` also crashed for guest callers
 * because `$user` was not typed as nullable.
 *
 * These tests lock both fixes in place.
 */
final class GateBeforeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bypasses_every_ability(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Any ability — even one that was never defined or granted — must
        // come back `true` for an admin thanks to the Gate::before bypass.
        $this->assertTrue(Gate::forUser($admin)->allows('totally-invented-ability'));
        $this->assertTrue(Gate::forUser($admin)->allows('users.manage'));
    }

    public function test_non_admin_without_permission_returns_false_not_recursion(): void
    {
        $user = User::factory()->create();

        // This is the exact call that used to blow the stack. We assert
        // it completes in a bounded number of frames and returns false.
        $this->assertFalse(Gate::forUser($user)->allows('users.manage'));
        $this->assertFalse(Gate::forUser($user)->allows('menus.edit'));
        $this->assertFalse(Gate::forUser($user)->allows('settings.manage'));
    }

    public function test_non_admin_with_granted_permission_returns_true(): void
    {
        Permission::findOrCreate('users.manage', 'web');

        $user = User::factory()->create();
        $user->givePermissionTo('users.manage');

        // Spatie's own Gate::before hook (registered by PermissionRegistrar)
        // resolves this without any help from AuthServiceProvider.
        $this->assertTrue(Gate::forUser($user->fresh())->allows('users.manage'));
    }

    public function test_gate_before_does_not_fatal_for_guest_user(): void
    {
        // No `forUser(...)` — the Gate::before closure receives null.
        // Before the fix, `$user->hasRole('admin')` would throw a
        // "Call to a member function hasRole() on null" fatal.
        $this->assertFalse(Gate::allows('users.manage'));
    }
}
