<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    | All knobs that previously lived as magic numbers inside
    | TwoFactorController. Override per-environment in `.env`.
    */

    'rate_limit' => [
        // Credential check (email + password) before issuing a 2FA challenge.
        'login' => [
            'max_attempts' => (int) env('TWOFACTOR_LOGIN_MAX_ATTEMPTS', 5),
            'decay_seconds' => (int) env('TWOFACTOR_LOGIN_DECAY_SECONDS', 60),
        ],
        // OTP code verification once the user holds a temporary challenge token.
        'challenge' => [
            'max_attempts' => (int) env('TWOFACTOR_CHALLENGE_MAX_ATTEMPTS', 5),
            'decay_seconds' => (int) env('TWOFACTOR_CHALLENGE_DECAY_SECONDS', 60),
        ],
    ],

    'recovery_codes' => [
        'count' => (int) env('TWOFACTOR_RECOVERY_CODE_COUNT', 8),
        'length' => (int) env('TWOFACTOR_RECOVERY_CODE_LENGTH', 10),
    ],

    'token' => [
        'name' => env('TWOFACTOR_TOKEN_NAME', 'api'),
        // Abilities granted to users who do NOT carry the `admin` role.
        'default_abilities' => [
            'articles:read',
            'comments:create',
            'me:read',
        ],
        // Abilities granted to admins.
        'admin_abilities' => ['*'],
    ],

    // How long the temporary "you proved your password, now prove TOTP" token
    // is considered valid. Embedded in the encrypted payload.
    'challenge_ttl_seconds' => (int) env('TWOFACTOR_CHALLENGE_TTL_SECONDS', 300),
];
