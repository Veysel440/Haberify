<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * A cached, strong, already-hashed password that satisfies the
     * project password policy. Tests that need the plaintext should
     * use `UserFactory::PASSWORD_PLAINTEXT` to authenticate.
     */
    public const PASSWORD_PLAINTEXT = 'CorrectHorse-Battery!9Staple';

    protected static ?string $cachedHash = null;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        self::$cachedHash ??= Hash::make(self::PASSWORD_PLAINTEXT);

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::$cachedHash,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
