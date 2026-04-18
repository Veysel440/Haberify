<?php

declare(strict_types=1);

namespace App\DTO\TwoFactor;

/**
 * Outcome of verifying a TOTP code against a previously issued
 * temporary challenge token.
 */
final readonly class TwoFactorChallengeResult
{
    public const STATUS_RATE_LIMITED = 'rate_limited';

    public const STATUS_INVALID_CHALLENGE = 'invalid_challenge';

    public const STATUS_INVALID_CODE = 'invalid_code';

    public const STATUS_AUTHENTICATED = 'authenticated';

    private function __construct(
        public string $status,
        public ?string $token = null,
    ) {}

    public static function rateLimited(): self
    {
        return new self(self::STATUS_RATE_LIMITED);
    }

    public static function invalidChallenge(): self
    {
        return new self(self::STATUS_INVALID_CHALLENGE);
    }

    public static function invalidCode(): self
    {
        return new self(self::STATUS_INVALID_CODE);
    }

    public static function authenticated(string $token): self
    {
        return new self(self::STATUS_AUTHENTICATED, token: $token);
    }
}
