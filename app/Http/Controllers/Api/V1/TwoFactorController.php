<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DTO\TwoFactor\LoginAttemptResult;
use App\DTO\TwoFactor\TwoFactorChallengeResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TwoFactor\VerifyCodeRequest;
use App\Http\Requests\Api\V1\TwoFactor\VerifyLoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\TwoFactor\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

/**
 * Pure plumbing: parse request → call service → translate result to HTTP.
 *
 * All 2FA business rules (encryption, rate limiting, TOTP verification,
 * token issuance, ability mapping) live in `App\Services\TwoFactor`.
 */
final class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorService $twoFactor) {}

    public function enable(Request $request): ApiResponse
    {
        $enrollment = $this->twoFactor->enroll($this->authenticatedUser($request));

        return ApiResponse::created($enrollment->toArray());
    }

    public function qrcode(Request $request): ApiResponse|JsonResponse
    {
        $url = $this->twoFactor->otpAuthUrlFor($this->authenticatedUser($request));

        if ($url === null) {
            return ApiResponse::error('Two-factor not enrolled.', 'two_factor.not_enrolled', HttpStatus::HTTP_NOT_FOUND)
                ->toResponse($request);
        }

        return ApiResponse::ok(['otpauth_url' => $url]);
    }

    public function verifyLogin(VerifyLoginRequest $request): ApiResponse|JsonResponse
    {
        $result = $this->twoFactor->attemptLogin(
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
            ip: (string) $request->ip(),
        );

        return $this->loginResultToResponse($result, $request);
    }

    public function verifyCode(VerifyCodeRequest $request): ApiResponse|JsonResponse
    {
        $result = $this->twoFactor->verifyChallenge(
            challengeToken: (string) $request->validated('tmp'),
            code: (string) $request->validated('code'),
            ip: (string) $request->ip(),
        );

        return $this->challengeResultToResponse($result, $request);
    }

    public function disable(Request $request): JsonResponse
    {
        $this->twoFactor->disable($this->authenticatedUser($request));

        return ApiResponse::noContent();
    }

    private function loginResultToResponse(LoginAttemptResult $result, Request $request): ApiResponse|JsonResponse
    {
        return match ($result->status) {
            LoginAttemptResult::STATUS_RATE_LIMITED => ApiResponse::error(
                'Too many attempts. Try again later.',
                'auth.rate_limited',
                HttpStatus::HTTP_TOO_MANY_REQUESTS,
            )->toResponse($request),

            LoginAttemptResult::STATUS_INVALID_CREDENTIALS => ApiResponse::error(
                'Invalid credentials.',
                'auth.invalid_credentials',
                HttpStatus::HTTP_UNPROCESSABLE_ENTITY,
            )->toResponse($request),

            LoginAttemptResult::STATUS_REQUIRES_TWO_FACTOR => ApiResponse::ok([
                'requires_2fa' => true,
                'tmp' => $result->challengeToken,
            ]),

            LoginAttemptResult::STATUS_AUTHENTICATED => ApiResponse::ok(['token' => $result->token]),

            default => ApiResponse::error('Unknown auth state.', 'auth.unknown', HttpStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->toResponse($request),
        };
    }

    private function challengeResultToResponse(TwoFactorChallengeResult $result, Request $request): ApiResponse|JsonResponse
    {
        return match ($result->status) {
            TwoFactorChallengeResult::STATUS_RATE_LIMITED => ApiResponse::error(
                'Two-factor locked. Try again later.',
                'two_factor.rate_limited',
                HttpStatus::HTTP_TOO_MANY_REQUESTS,
            )->toResponse($request),

            TwoFactorChallengeResult::STATUS_INVALID_CHALLENGE => ApiResponse::error(
                'Challenge expired or invalid.',
                'two_factor.invalid_challenge',
                HttpStatus::HTTP_UNPROCESSABLE_ENTITY,
            )->toResponse($request),

            TwoFactorChallengeResult::STATUS_INVALID_CODE => ApiResponse::error(
                'Invalid code.',
                'two_factor.invalid_code',
                HttpStatus::HTTP_UNPROCESSABLE_ENTITY,
            )->toResponse($request),

            TwoFactorChallengeResult::STATUS_AUTHENTICATED => ApiResponse::ok(['token' => $result->token]),

            default => ApiResponse::error('Unknown 2FA state.', 'two_factor.unknown', HttpStatus::HTTP_INTERNAL_SERVER_ERROR)
                ->toResponse($request),
        };
    }

    private function authenticatedUser(Request $request): User
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_if($user === null, HttpStatus::HTTP_UNAUTHORIZED);

        return $user;
    }
}
