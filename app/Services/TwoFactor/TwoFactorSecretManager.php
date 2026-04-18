<?php

declare(strict_types=1);

namespace App\Services\TwoFactor;

use App\DTO\TwoFactor\TwoFactorEnrollmentData;
use App\Models\User;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Owns every read/write of the user's encrypted TOTP material.
 *
 * The controller never sees plaintext secrets or recovery codes
 * outside the brief moment they are returned during enrolment.
 */
final readonly class TwoFactorSecretManager
{
    public function __construct(
        private Google2FA $google2fa,
        private Encrypter $encrypter,
        private ConfigRepository $config,
    ) {}

    /**
     * Generate a fresh secret + recovery codes, persist the encrypted
     * material on the user, and return everything the user needs to
     * complete enrolment in their authenticator app.
     *
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function enroll(User $user): TwoFactorEnrollmentData
    {
        $secret = $this->google2fa->generateSecretKey();
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $this->encrypter->encrypt($secret),
            'two_factor_recovery_codes' => $this->encrypter->encrypt(
                json_encode($recoveryCodes, JSON_THROW_ON_ERROR),
            ),
            'two_factor_confirmed_at' => null,
        ])->save();

        return new TwoFactorEnrollmentData(
            secret: $secret,
            otpAuthUrl: $this->buildOtpAuthUrl($user, $secret),
            recoveryCodes: $recoveryCodes,
        );
    }

    /**
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function buildOtpAuthUrl(User $user, string $secret): string
    {
        /** @var string $issuer */
        $issuer = $this->config->get('app.name', 'Laravel');

        return $this->google2fa->getQRCodeUrl($issuer, (string) $user->getAttribute('email'), $secret);
    }

    /**
     * Reveal the OTPAuth URL for an already-enrolled user, e.g. for re-display
     * on a settings screen. Returns null if the user is not enrolled.
     *
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function otpAuthUrlFor(User $user): ?string
    {
        $secret = $this->decryptSecret($user);

        if ($secret === null) {
            return null;
        }

        return $this->buildOtpAuthUrl($user, $secret);
    }

    public function verifyCode(User $user, string $code): bool
    {
        $secret = $this->decryptSecret($user);

        if ($secret === null) {
            return false;
        }

        return $this->google2fa->verifyKey($secret, $code);
    }

    public function isEnrolled(User $user): bool
    {
        return ! empty($user->getAttribute('two_factor_secret'));
    }

    public function markConfirmed(User $user): void
    {
        $user->forceFill(['two_factor_confirmed_at' => Date::now()])->save();
    }

    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    private function decryptSecret(User $user): ?string
    {
        $cipher = $user->getAttribute('two_factor_secret');

        if ($cipher === null || $cipher === '') {
            return null;
        }

        /** @var string */
        return $this->encrypter->decrypt((string) $cipher);
    }

    /**
     * @return list<string>
     */
    private function generateRecoveryCodes(): array
    {
        $count = (int) $this->config->get('twofactor.recovery_codes.count', 8);
        $length = (int) $this->config->get('twofactor.recovery_codes.length', 10);

        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::random($length);
        }

        return $codes;
    }
}
