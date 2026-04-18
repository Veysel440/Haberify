<?php

declare(strict_types=1);

namespace App\Services\TwoFactor;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\Date;
use Throwable;

/**
 * Encodes/decodes the short-lived "you proved your password, now prove TOTP"
 * token that lives on the client between the two HTTP roundtrips.
 *
 * Encryption protects against tampering; the embedded timestamp lets us
 * enforce a TTL even though the token never hits the server's cache.
 */
final readonly class TwoFactorChallengeTokenizer
{
    public function __construct(
        private Encrypter $encrypter,
        private ConfigRepository $config,
    ) {}

    public function issue(int $userId): string
    {
        return $this->encrypter->encrypt([
            'id' => $userId,
            'ts' => Date::now()->getTimestamp(),
        ]);
    }

    /**
     * Returns the user id if the token is valid and unexpired, null otherwise.
     */
    public function decode(string $token): ?int
    {
        try {
            /** @var array{id?: int|string, ts?: int}|mixed $payload */
            $payload = $this->encrypter->decrypt($token);
        } catch (Throwable) {
            return null;
        }

        if (! is_array($payload) || ! isset($payload['id'], $payload['ts'])) {
            return null;
        }

        if ($this->isExpired((int) $payload['ts'])) {
            return null;
        }

        return (int) $payload['id'];
    }

    private function isExpired(int $issuedAt): bool
    {
        $ttl = (int) $this->config->get('twofactor.challenge_ttl_seconds', 300);

        return Date::now()->getTimestamp() - $issuedAt > $ttl;
    }
}
