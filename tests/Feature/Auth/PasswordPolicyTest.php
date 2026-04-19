<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Http\Requests\Api\V1\Auth\AuthFormRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Policy invariants (config/security.php):
 *   - min 12 chars
 *   - mixed case
 *   - numbers
 *   - symbols
 *   - uncompromised (disabled in test env to avoid HIBP network call)
 *
 * We validate the FormRequest rule arrays directly instead of hitting HTTP
 * routes because register / reset endpoints are not currently wired to the
 * router. The rule array is what we're actually guarding.
 */
final class PasswordPolicyTest extends TestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function weakPasswords(): array
    {
        return [
            'too short' => ['Aa1!short'],
            'no uppercase' => ['abcdefgh123!@#'],
            'no lowercase' => ['ABCDEFGH123!@#'],
            'no digits' => ['AbcdefghIjk!@#'],
            'no symbols' => ['AbcdefghIjk123'],
            'length exactly 11 (< 12)' => ['Aa1!aaaaaaa'],
        ];
    }

    /**
     * @return array<string, array{0: class-string<AuthFormRequest>}>
     */
    public static function requestClasses(): array
    {
        return [
            'register' => [RegisterRequest::class],
            'reset' => [ResetPasswordRequest::class],
        ];
    }

    /**
     * Cartesian product of {register, reset} × weakPasswords.
     *
     * @return array<string, array{0: class-string<AuthFormRequest>, 1: string}>
     */
    public static function weakOnEachRequest(): array
    {
        $cases = [];
        foreach (self::requestClasses() as $rqKey => [$rqClass]) {
            foreach (self::weakPasswords() as $pwKey => [$pw]) {
                $cases["$rqKey / $pwKey"] = [$rqClass, $pw];
            }
        }

        return $cases;
    }

    #[DataProvider('weakOnEachRequest')]
    public function test_rejects_weak_password(string $formRequestClass, string $password): void
    {
        $validator = $this->validatePasswordFor($formRequestClass, $password);

        $this->assertTrue($validator->fails(), 'Weak password should have failed validation.');
        $this->assertTrue(
            $validator->errors()->has('password'),
            'Password field should carry the failure.',
        );
    }

    #[DataProvider('requestClasses')]
    public function test_accepts_a_strong_password(string $formRequestClass): void
    {
        $strong = 'CorrectHorse-Battery!9Staple';
        $validator = $this->validatePasswordFor($formRequestClass, $strong);

        // Other fields (e.g. unique email on register) may legitimately
        // fail in isolation — we only guard the password rule here.
        $this->assertFalse(
            $validator->errors()->has('password'),
            sprintf(
                'Strong password should have passed. Got: %s',
                (string) $validator->errors()->first('password'),
            ),
        );
    }

    #[DataProvider('requestClasses')]
    public function test_rejects_mismatched_password_confirmation(string $formRequestClass): void
    {
        $payload = $this->payloadFor($formRequestClass, 'CorrectHorse-Battery!9Staple');
        $payload['password_confirmation'] = 'Something-Else!9Staple';

        $validator = $this->makeValidator($formRequestClass, $payload);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('password'));
    }

    /**
     * @param class-string<AuthFormRequest> $formRequestClass
     */
    private function validatePasswordFor(string $formRequestClass, string $password): ValidatorContract
    {
        return $this->makeValidator(
            $formRequestClass,
            $this->payloadFor($formRequestClass, $password),
        );
    }

    /**
     * @param class-string<AuthFormRequest> $formRequestClass
     * @param array<string, mixed> $payload
     */
    private function makeValidator(string $formRequestClass, array $payload): ValidatorContract
    {
        $instance = new $formRequestClass;

        return Validator::make($payload, $instance->rules());
    }

    /**
     * @param class-string<AuthFormRequest> $formRequestClass
     *
     * @return array<string, mixed>
     */
    private function payloadFor(string $formRequestClass, string $password): array
    {
        return match ($formRequestClass) {
            RegisterRequest::class => [
                'name' => 'Jane Doe',
                'email' => 'jane.' . uniqid() . '@example.com',
                'password' => $password,
                'password_confirmation' => $password,
            ],
            ResetPasswordRequest::class => [
                'token' => 'any-non-empty-token',
                'email' => 'jane.' . uniqid() . '@example.com',
                'password' => $password,
                'password_confirmation' => $password,
            ],
            default => throw new InvalidArgumentException('Unsupported request class'),
        };
    }
}
