<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): mixed
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return ApiResponse::ok(['token' => $token]);
    }

    public function login(Request $r): mixed
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);

        if (! auth()->attempt($cred)) {
            return ApiResponse::error('Invalid credentials', 422);
        }
        $token = $r->user()->createToken('api')->plainTextToken;

        return ApiResponse::ok(['token' => $token]);
    }

    public function me(Request $r): mixed
    {
        return ApiResponse::ok($r->user());
    }

    public function logout(Request $r): mixed
    {
        $r->user()->currentAccessToken()->delete();

        return ApiResponse::ok();
    }
}
