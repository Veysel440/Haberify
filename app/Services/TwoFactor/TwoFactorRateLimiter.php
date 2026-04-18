<?php

declare(strict_types=1);

namespace App\Services\TwoFactor;

use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\Str;

/**
 * Thin policy wrapper around Laravel's RateLimiter, named so the call sites
 * read like business rules instead of cache keys + magic numbers.
 *
 * The underlying `Illuminate\Cache\RateLimiter` is bound by the framework,
 * so we get the same store the framework's `throttle:` middleware uses.
 */
final readonly class TwoFactorRateLimiter
{
    private const SCOPE_LOGIN = 'login';

    private const SCOPE_CHALLENGE = 'challenge';

    public function __construct(
        private RateLimiter $limiter,
        private ConfigRepository $config,
    ) {}

    public function isLoginLocked(string $email, string $ip): bool
    {
        return $this->isLocked(self::SCOPE_LOGIN, $this->loginKey($email, $ip));
    }

    public function recordFailedLogin(string $email, string $ip): void
    {
        $this->recordFailure(self::SCOPE_LOGIN, $this->loginKey($email, $ip));
    }

    public function clearLogin(string $email, string $ip): void
    {
        $this->limiter->clear($this->loginKey($email, $ip));
    }

    public function isChallengeLocked(int $userId, string $ip): bool
    {
        return $this->isLocked(self::SCOPE_CHALLENGE, $this->challengeKey($userId, $ip));
    }

    public function recordFailedChallenge(int $userId, string $ip): void
    {
        $this->recordFailure(self::SCOPE_CHALLENGE, $this->challengeKey($userId, $ip));
    }

    public function clearChallenge(int $userId, string $ip): void
    {
        $this->limiter->clear($this->challengeKey($userId, $ip));
    }

    private function isLocked(string $scope, string $key): bool
    {
        return $this->limiter->tooManyAttempts($key, $this->maxAttempts($scope));
    }

    private function recordFailure(string $scope, string $key): void
    {
        $this->limiter->hit($key, $this->decaySeconds($scope));
    }

    private function loginKey(string $email, string $ip): string
    {
        return self::SCOPE_LOGIN . ':' . Str::lower($email) . ':' . $ip;
    }

    private function challengeKey(int $userId, string $ip): string
    {
        return self::SCOPE_CHALLENGE . ':' . $userId . ':' . $ip;
    }

    private function maxAttempts(string $scope): int
    {
        return (int) $this->config->get("twofactor.rate_limit.{$scope}.max_attempts", 5);
    }

    private function decaySeconds(string $scope): int
    {
        return (int) $this->config->get("twofactor.rate_limit.{$scope}.decay_seconds", 60);
    }
}
