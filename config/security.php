<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    |
    | These values drive `Illuminate\Validation\Rules\Password::defaults()`
    | registered in `AppServiceProvider::boot()`. Every user-facing write
    | path (registration, password reset, admin-initiated password set)
    | MUST use `Password::defaults()` so the policy stays a single source
    | of truth.
    |
    | Baseline (OWASP ASVS 4.0 §2.1, NIST SP 800-63B §5.1.1.2):
    |   - 12+ characters
    |   - Mixed case, numbers, symbols
    |   - Rejected if found in a public breach corpus (HIBP k-anonymity)
    |
    | The `uncompromised_check` flag exists ONLY so the test suite can turn
    | off the outbound HIBP lookup. Do not disable it in production.
    */

    'password' => [
        'min_length' => (int) env('PASSWORD_MIN_LENGTH', 12),
        'require_mixed_case' => (bool) env('PASSWORD_REQUIRE_MIXED_CASE', true),
        'require_numbers' => (bool) env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => (bool) env('PASSWORD_REQUIRE_SYMBOLS', true),
        'uncompromised_check' => (bool) env('PASSWORD_UNCOMPROMISED_CHECK', true),
        'uncompromised_threshold' => (int) env('PASSWORD_UNCOMPROMISED_THRESHOLD', 0),
    ],

];
