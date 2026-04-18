<?php

declare(strict_types=1);

namespace App\DTO\TwoFactor;

/**
 * Outcome of a credential-based login attempt.
 *
 * Sealed-style: instances must be created via the named constructors.
 * This makes the four legitimate outcomes (rate-limited, invalid,
 * needs-2FA, authenticated) exhaustive at the call site without
 * resorting to exceptions for expected control flow.
 */
final readonly class LoginAttemptResult
{
    public const STATUS_RATE_LIMITED = 'rate_limited';

    public const STATUS_INVALID_CREDENTIALS = 'invalid_credentials';

    public const STATUS_REQUIRES_TWO_FACTOR = 'requires_two_factor';

    public const STATUS_AUTHENTICATED = 'authenticated';

    private function __construct(
        public string $status,
        public ?string $token = null,
        public ?string $challengeToken = null,
    ) {}

    public static function rateLimited(): self
    {
        return new self(self::STATUS_RATE_LIMITED);
    }

    public static function invalidCredentials(): self
    {
        return new self(self::STATUS_INVALID_CREDENTIALS);
    }

    public static function requiresTwoFactor(string $challengeToken): self
    {
        return new self(self::STATUS_REQUIRES_TWO_FACTOR, challengeToken: $challengeToken);
    }

    public static function authenticated(string $token): self
    {
        return new self(self::STATUS_AUTHENTICATED, token: $token);
    }
}
