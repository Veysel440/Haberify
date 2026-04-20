<?php

declare(strict_types=1);

namespace Tests\Feature\ActivityLog;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Locks in the activity-log coverage + sensitive-data exclusion contract
 * for every LogsActivity-enabled model. If a new sensitive column is
 * added and forgotten, these tests fail loudly instead of silently
 * leaking the value into `activity_log.properties`.
 */
final class SensitiveDataGuardTest extends TestCase
{
    use RefreshDatabase;

    // --- Coverage: every model produces an activity row on update ------------

    public function test_category_update_is_logged(): void
    {
        $c = Category::factory()->create(['name' => 'Politics']);
        $c->update(['name' => 'World']);

        $activity = Activity::where('log_name', 'category')->latest('id')->first();

        $this->assertNotNull($activity);
        $this->assertSame('updated', $activity->event);
        $this->assertSame('World', $activity->properties['attributes']['name'] ?? null);
    }

    public function test_tag_update_is_logged(): void
    {
        $t = Tag::factory()->create(['name' => 'breaking']);
        $t->update(['name' => 'headline']);

        $this->assertNotNull(
            Activity::where('log_name', 'tag')->latest('id')->first(),
        );
    }

    public function test_comment_update_is_logged(): void
    {
        $c = Comment::factory()->create(['status' => 'pending']);
        $c->update(['status' => 'approved']);

        $activity = Activity::where('log_name', 'comment')->latest('id')->first();
        $this->assertNotNull($activity);
        $this->assertSame('approved', $activity->properties['attributes']['status'] ?? null);
    }

    public function test_page_update_is_logged(): void
    {
        $p = Page::create([
            'title' => 'About',
            'slug' => 'about',
            'body' => '<p>v1</p>',
            'status' => 'draft',
        ]);
        $p->update(['status' => 'published']);

        $this->assertNotNull(
            Activity::where('log_name', 'page')->latest('id')->first(),
        );
    }

    public function test_menu_update_is_logged(): void
    {
        $m = Menu::create(['name' => 'main', 'items' => []]);
        $m->update(['items' => [['label' => 'Home', 'url' => '/']]]);

        $this->assertNotNull(
            Activity::where('log_name', 'menu')->latest('id')->first(),
        );
    }

    // --- Sensitive guards -----------------------------------------------------

    public function test_user_password_change_is_never_recorded_in_activity_log(): void
    {
        $user = User::factory()->create(['email' => 'audit@example.com']);

        // Cause a set of writes that cover every sensitive column.
        $user->update([
            'password' => Hash::make('BrandNewStrong-Pw!2026'),
            'name' => 'Renamed',
        ]);
        $user->forceFill([
            'remember_token' => Str::random(40),
            'two_factor_secret' => 'encrypted-secret-cipher',
            'two_factor_recovery_codes' => 'encrypted-codes-cipher',
            'two_factor_confirmed_at' => now(),
            'is_comment_banned' => true,
            'comment_banned_until' => now()->addDay(),
            'comment_ban_reason' => 'spam',
        ])->save();

        $activities = Activity::where('log_name', 'user')->get();
        $this->assertNotEmpty($activities, 'User updates must produce at least one activity row.');

        foreach ($activities as $activity) {
            $payload = json_encode($activity->properties, JSON_THROW_ON_ERROR);

            foreach (User::SENSITIVE_ATTRIBUTES as $forbidden) {
                $this->assertStringNotContainsString(
                    $forbidden,
                    $payload,
                    "Activity row leaked sensitive attribute name `{$forbidden}`: {$payload}",
                );
            }

            // Extra belt-and-suspenders: the raw ciphertext strings we
            // planted above must never appear either (defence against
            // the case where an attribute was renamed but the check above
            // relied on the literal name).
            $this->assertStringNotContainsString('encrypted-secret-cipher', $payload);
            $this->assertStringNotContainsString('encrypted-codes-cipher', $payload);
        }

        // Non-sensitive change (`name`) must still be captured — otherwise
        // the guard would be too broad and the audit trail would lose value.
        $this->assertTrue(
            $activities->contains(fn (Activity $a) => ($a->properties['attributes']['name'] ?? null) === 'Renamed'),
            'Safe attribute changes (name) must still be audited.',
        );
    }

    public function test_setting_value_is_never_recorded_in_activity_log(): void
    {
        // Simulate a secret-bearing setting update (API key rotation).
        $setting = Setting::create([
            'key' => 'payment.api_key',
            'value' => ['token' => 'live_sk_OLD_supersecret_abc123'],
        ]);

        $setting->update([
            'value' => ['token' => 'live_sk_NEW_supersecret_xyz789'],
        ]);

        $activities = Activity::where('log_name', 'setting')->get();
        $this->assertNotEmpty($activities, 'Setting writes must produce at least one activity row.');

        foreach ($activities as $activity) {
            $payload = json_encode($activity->properties, JSON_THROW_ON_ERROR);

            // Neither the old nor the new secret may appear anywhere.
            $this->assertStringNotContainsString('live_sk_OLD_supersecret_abc123', $payload);
            $this->assertStringNotContainsString('live_sk_NEW_supersecret_xyz789', $payload);
            // And the `value` key itself must never make it into properties.
            $this->assertArrayNotHasKey('value', $activity->properties['attributes'] ?? []);
            $this->assertArrayNotHasKey('value', $activity->properties['old'] ?? []);
        }

        // But the key itself IS captured — we want to know which setting
        // someone touched, even if we don't reveal the value.
        $keyEvent = $activities->first(
            fn (Activity $a) => ($a->properties['attributes']['key'] ?? null) === 'payment.api_key',
        );
        $this->assertNotNull($keyEvent, 'The setting key must appear in the audit trail.');
    }
}
