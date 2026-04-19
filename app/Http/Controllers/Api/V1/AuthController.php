<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Auth\SanctumTokenIssuer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Sign-up / identity endpoints.
 *
 * The login path lives in `TwoFactorController::verifyLogin` because every
 * authentication attempt must run through the 2FA challenge flow. A second
 * "plain login" here would be a silent bypass and is intentionally absent.
 *
 * Token issuance is delegated to `SanctumTokenIssuer` — the single source of
 * truth for role → ability mapping and token name. Calling `createToken(...)`
 * directly in this controller is a DRY + security regression: it would give
 * newly-registered admins default abilities instead of admin abilities.
 *
 * All responses flow through `App\Http\Responses\ApiResponse`.
 */
class AuthController extends Controller
{
    public function __construct(private readonly SanctumTokenIssuer $tokens) {}

    public function register(RegisterRequest $request): ApiResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return ApiResponse::created(['token' => $this->tokens->issueFor($user)]);
    }

    public function me(Request $request): ApiResponse
    {
        return ApiResponse::ok($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::noContent();
    }
}
