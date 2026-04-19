<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * End-to-end HTTP coverage for the newly wired auth endpoints:
 *   POST /api/v1/auth/register
 *   POST /api/v1/auth/forgot-password
 *   POST /api/v1/auth/reset-password
 *   GET  /api/v1/auth/me
 *   POST /api/v1/auth/logout
 *
 * Each test goes through the real router + middleware + FormRequest stack,
 * so we're covering the whole path — not just the rule arrays.
 */
final class AuthEndpointsTest extends TestCase
{
    use RefreshDatabase;

    // --- register -----------------------------------------------------------

    public function test_register_rejects_weak_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_register_accepts_strong_password_and_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'CorrectHorse-Battery!9Staple',
            'password_confirmation' => 'CorrectHorse-Battery!9Staple',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertNotSame('CorrectHorse-Battery!9Staple', $user->password);
        $this->assertTrue(Hash::check('CorrectHorse-Battery!9Staple', $user->password));
    }

    public function test_register_issues_token_with_configured_abilities_not_wildcard(): void
    {
        config()->set('twofactor.token.default_abilities', ['articles:read', 'comments:create', 'me:read']);
        config()->set('twofactor.token.admin_abilities', ['*']);
        config()->set('twofactor.token.name', 'api');

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Non Admin',
            'email' => 'regular@example.com',
            'password' => 'CorrectHorse-Battery!9Staple',
            'password_confirmation' => 'CorrectHorse-Battery!9Staple',
        ])->assertCreated();

        $user = User::where('email', 'regular@example.com')->firstOrFail();
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->tokens()->latest('id')->firstOrFail();

        // A freshly-registered user MUST NOT receive the wildcard `*` ability.
        // This guards the bug where AuthController::register bypassed
        // SanctumTokenIssuer and called createToken('api') directly, which
        // would have produced a token with `['*']` abilities.
        $this->assertSame('api', $token->name);
        $this->assertNotContains('*', $token->abilities);
        $this->assertSame(['articles:read', 'comments:create', 'me:read'], $token->abilities);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Jane Doe',
            'email' => 'taken@example.com',
            'password' => 'CorrectHorse-Battery!9Staple',
            'password_confirmation' => 'CorrectHorse-Battery!9Staple',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    // --- forgot-password ----------------------------------------------------

    public function test_forgot_password_requires_valid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_for_known_email_dispatches_reset(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'known@example.com']);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'known@example.com',
        ]);

        $response->assertOk();
    }

    // --- reset-password -----------------------------------------------------

    public function test_reset_password_rejects_weak_password(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        $token = $this->passwordBroker()->createToken($user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_reset_password_succeeds_with_strong_password_and_revokes_tokens(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        // Seed an active API token — it must be revoked after reset.
        $user->createToken('api');
        $this->assertSame(1, $user->tokens()->count());

        $token = $this->passwordBroker()->createToken($user);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'CorrectHorse-Battery!9Staple',
            'password_confirmation' => 'CorrectHorse-Battery!9Staple',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('CorrectHorse-Battery!9Staple', $user->fresh()?->password));
        $this->assertSame(0, $user->tokens()->count(), 'Active tokens should be revoked on password reset.');
    }

    // --- me / logout --------------------------------------------------------

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_me_returns_current_user_when_authenticated(): void
    {
        $user = User::factory()->create(['email' => 'me@example.com']);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.email', 'me@example.com');

        // UserResource allowlisted shape — if this ever grows to include
        // raw model attributes, the `does not leak` test below will catch it.
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id', 'name', 'email', 'avatar', 'bio', 'role', 'status',
                'email_verified', 'two_factor_enabled', 'roles', 'permissions',
                'created_at', 'updated_at',
            ],
        ]);

        // Derived boolean, not the raw timestamp / cipher.
        $response->assertJsonPath('data.two_factor_enabled', false);
    }

    public function test_me_does_not_leak_sensitive_user_columns(): void
    {
        // Seed a user with all the server-only columns populated so that
        // any accidental `$user->toArray()` leak would show up in the JSON.
        $user = User::factory()->create(['email' => 'leak@example.com']);
        $user->forceFill([
            'two_factor_secret' => 'encrypted-secret-cipher',
            'two_factor_recovery_codes' => 'encrypted-codes-cipher',
            'two_factor_confirmed_at' => now(),
            'is_comment_banned' => true,
            'comment_banned_until' => now()->addDay(),
            'comment_ban_reason' => 'test',
            'remember_token' => 'remember-me',
        ])->save();

        $response = $this->actingAs($user->fresh(), 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk();

        $payload = $response->json('data');

        // None of these should ever leave the server.
        $forbidden = [
            'password', 'remember_token',
            'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
            'is_comment_banned', 'comment_banned_until', 'comment_ban_reason',
        ];

        foreach ($forbidden as $key) {
            $this->assertArrayNotHasKey($key, $payload, "Sensitive field `{$key}` leaked via /auth/me.");
        }

        // …but the safe, derived boolean must still be present and correct.
        $this->assertTrue($payload['two_factor_enabled']);
    }

    private function passwordBroker(): PasswordBroker
    {
        $broker = Password::broker();
        $this->assertInstanceOf(PasswordBroker::class, $broker);

        return $broker;
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $plain = $user->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $plain)
            ->postJson('/api/v1/auth/logout')
            ->assertNoContent();

        $this->assertSame(0, $user->tokens()->count());
    }
}
