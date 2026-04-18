<?php

declare(strict_types=1);

namespace App\DTO\TwoFactor;

/**
 * Returned to the user the one and only time they enable 2FA.
 * Recovery codes are plaintext here — they MUST NOT be stored
 * anywhere outside the user's own vault after this response.
 */
final readonly class TwoFactorEnrollmentData
{
    /**
     * @param list<string> $recoveryCodes
     */
    public function __construct(
        public string $secret,
        public string $otpAuthUrl,
        public array $recoveryCodes,
    ) {}

    /**
     * @return array{secret: string, otpauth_url: string, recovery_codes: list<string>}
     */
    public function toArray(): array
    {
        return [
            'secret' => $this->secret,
            'otpauth_url' => $this->otpAuthUrl,
            'recovery_codes' => $this->recoveryCodes,
        ];
    }
}
