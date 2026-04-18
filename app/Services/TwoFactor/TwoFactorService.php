<?php

declare(strict_types=1);

namespace App\Services\TwoFactor;

use App\DTO\TwoFactor\LoginAttemptResult;
use App\DTO\TwoFactor\TwoFactorChallengeResult;
use App\DTO\TwoFactor\TwoFactorEnrollmentData;
use App\Models\User;
use App\Services\Auth\SanctumTokenIssuer;
use Illuminate\Contracts\Auth\StatefulGuard;

/**
 * Public-facing facade for the entire two-factor flow.
 *
 * Composed of small collaborators (rate limiter, secret manager, challenge
 * tokenizer, token issuer). Each collaborator has a single reason to change;
 * this class only orchestrates them.
 */
final readonly class TwoFactorService
{
    public function __construct(
        private TwoFactorSecretManager $secrets,
        private TwoFactorRateLimiter $rateLimiter,
        private TwoFactorChallengeTokenizer $challenges,
        private SanctumTokenIssuer $tokens,
        private StatefulGuard $guard,
    ) {}

    public function enroll(User $user): TwoFactorEnrollmentData
    {
        return $this->secrets->enroll($user);
    }

    public function otpAuthUrlFor(User $user): ?string
    {
        return $this->secrets->otpAuthUrlFor($user);
    }

    public function disable(User $user): void
    {
        $this->secrets->disable($user);
    }

    /**
     * Step 1 of the login flow: validate credentials, then either issue a
     * full token (no 2FA enrolled) or a temporary challenge token.
     */
    public function attemptLogin(string $email, string $password, string $ip): LoginAttemptResult
    {
        if ($this->rateLimiter->isLoginLocked($email, $ip)) {
            return LoginAttemptResult::rateLimited();
        }

        $credentials = ['email' => $email, 'password' => $password];

        if (! $this->guard->validate($credentials)) {
            $this->rateLimiter->recordFailedLogin($email, $ip);

            return LoginAttemptResult::invalidCredentials();
        }

        $this->rateLimiter->clearLogin($email, $ip);

        $user = User::query()->where('email', $email)->firstOrFail();

        if (! $this->secrets->isEnrolled($user)) {
            return LoginAttemptResult::authenticated($this->tokens->issueFor($user));
        }

        return LoginAttemptResult::requiresTwoFactor(
            $this->challenges->issue((int) $user->getKey()),
        );
    }

    /**
     * Step 2 of the login flow: verify the TOTP code against the challenge
     * token issued in step 1.
     */
    public function verifyChallenge(string $challengeToken, string $code, string $ip): TwoFactorChallengeResult
    {
        $userId = $this->challenges->decode($challengeToken);

        if ($userId === null) {
            return TwoFactorChallengeResult::invalidChallenge();
        }

        $user = User::query()->find($userId);

        if ($user === null) {
            return TwoFactorChallengeResult::invalidChallenge();
        }

        if ($this->rateLimiter->isChallengeLocked($userId, $ip)) {
            return TwoFactorChallengeResult::rateLimited();
        }

        if (! $this->secrets->verifyCode($user, $code)) {
            $this->rateLimiter->recordFailedChallenge($userId, $ip);

            return TwoFactorChallengeResult::invalidCode();
        }

        $this->rateLimiter->clearChallenge($userId, $ip);
        $this->secrets->markConfirmed($user);

        return TwoFactorChallengeResult::authenticated($this->tokens->issueFor($user));
    }
}
