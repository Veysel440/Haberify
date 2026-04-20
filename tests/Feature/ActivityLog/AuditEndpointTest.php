<?php

declare(strict_types=1);

namespace Tests\Feature\ActivityLog;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * End-to-end HTTP coverage for `GET /api/v1/audit`.
 *
 * Exercises the full stack: Sanctum auth → Spatie permission gate →
 * `AuditIndexRequest` validation → `AuditController::index` query +
 * pagination → `AuditLogEntryResource` shape → `ApiResponse` envelope.
 */
final class AuditEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user->fresh() ?? $user;
    }

    private function viewer(): User
    {
        Permission::findOrCreate('analytics.view', 'web');
        $user = User::factory()->create();
        $user->givePermissionTo('analytics.view');

        return $user->fresh() ?? $user;
    }

    private function seedActivities(int $count, string $logName = 'category'): void
    {
        // Each category update produces one activity row.
        $category = Category::factory()->create();
        for ($i = 0; $i < $count; $i++) {
            $category->update(['name' => "Cat {$i}"]);
        }
        $this->assertSame($logName, 'category');
    }

    // ─── Authorisation ──────────────────────────────────────────────────────

    public function test_endpoint_requires_authentication(): void
    {
        $this->getJson('/api/v1/audit')->assertStatus(401);
    }

    public function test_endpoint_requires_analytics_view_permission(): void
    {
        $noPermission = User::factory()->create();

        $this->actingAs($noPermission, 'sanctum')
            ->getJson('/api/v1/audit')
            ->assertForbidden();
    }

    public function test_permission_holder_can_read_audit(): void
    {
        $this->actingAs($this->viewer(), 'sanctum')
            ->getJson('/api/v1/audit')
            ->assertOk();
    }

    // ─── Envelope + pagination ──────────────────────────────────────────────

    public function test_response_uses_project_api_response_envelope(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit')
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => [
                    'pagination' => [
                        'current_page', 'per_page', 'total', 'last_page',
                    ],
                    'filters',
                ],
            ])
            ->assertJsonPath('success', true);
    }

    public function test_results_are_paginated_with_configurable_per_page(): void
    {
        $this->seedActivities(12);

        $response = $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?per_page=5')
            ->assertOk();

        $this->assertCount(5, $response->json('data'));
        $this->assertSame(5, $response->json('meta.pagination.per_page'));
        $this->assertGreaterThanOrEqual(12, (int) $response->json('meta.pagination.total'));
    }

    public function test_per_page_is_capped_at_100(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?per_page=9999')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    // ─── Filters ────────────────────────────────────────────────────────────

    public function test_filters_by_log_name(): void
    {
        // Produce rows under two different log names.
        $cat = Category::factory()->create();
        $cat->update(['name' => 'Renamed']);

        $user = User::factory()->create();
        $user->update(['name' => 'Renamed User']);

        $response = $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?log_name=category')
            ->assertOk();

        $logNames = array_column($response->json('data'), 'log_name');
        $this->assertNotEmpty($logNames, 'At least one category audit row expected.');
        $this->assertSame(['category'], array_values(array_unique($logNames)));
        $this->assertSame('category', $response->json('meta.filters.log_name'));
    }

    public function test_unknown_log_name_is_rejected_by_validation(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?log_name=nonexistent')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['log_name']);
    }

    public function test_filters_by_date_range(): void
    {
        $cat = Category::factory()->create();
        $cat->update(['name' => 'Today-event']);

        $today = now()->format('Y-m-d');

        $response = $this->actingAs($this->admin(), 'sanctum')
            ->getJson("/api/v1/audit?from={$today}&to={$today}")
            ->assertOk();

        $this->assertGreaterThanOrEqual(1, (int) $response->json('meta.pagination.total'));
        $this->assertSame($today, $response->json('meta.filters.from'));
        $this->assertSame($today, $response->json('meta.filters.to'));

        // A far-past date range returns zero rows but still succeeds.
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?from=2000-01-01&to=2000-12-31')
            ->assertOk()
            ->assertJsonPath('meta.pagination.total', 0)
            ->assertJsonPath('data', []);
    }

    public function test_to_must_be_after_or_equal_from(): void
    {
        $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?from=2026-05-01&to=2026-04-01')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['to']);
    }

    public function test_filters_by_causer_id(): void
    {
        $actor = $this->admin();

        // Actor performs the audited action explicitly so their id becomes
        // the causer on the resulting activity row.
        $this->actingAs($actor);
        Category::factory()->create()->update(['name' => 'Acted-on']);

        $other = User::factory()->create();

        $response = $this->actingAs($actor, 'sanctum')
            ->getJson("/api/v1/audit?causer_id={$other->id}")
            ->assertOk();

        // Nobody has caused anything under `other`'s id → zero rows.
        $this->assertSame(0, $response->json('meta.pagination.total'));
        $this->assertSame($other->id, $response->json('meta.filters.causer_id'));
    }

    // ─── Resource shape ─────────────────────────────────────────────────────

    public function test_resource_shape_is_allowlisted(): void
    {
        Category::factory()->create()->update(['name' => 'Shape check']);

        $response = $this->actingAs($this->admin(), 'sanctum')
            ->getJson('/api/v1/audit?log_name=category&per_page=1')
            ->assertOk();

        $row = $response->json('data.0');
        $this->assertIsArray($row);

        // Allowlisted keys only.
        $expected = ['id', 'log_name', 'event', 'description', 'subject', 'causer', 'properties', 'batch_uuid', 'created_at'];
        sort($expected);
        $actual = array_keys($row);
        sort($actual);
        $this->assertSame($expected, $actual, 'Audit row keys drifted from the allowlist.');
    }
}
