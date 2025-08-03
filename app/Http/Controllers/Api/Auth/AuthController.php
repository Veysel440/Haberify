<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success([
                'user'  => new UserResource($user),
                'token' => $token,
            ], "Kayıt başarılı.", 201);

        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422, $e->errors());
        } catch (\Throwable $e) {
            \Log::error($e);
            return ApiResponse::error("Kayıt sırasında hata oluştu.", 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return ApiResponse::error("E-posta veya şifre hatalı.", 401);
            }

            if ($user->status === 'banned') {
                return ApiResponse::error("Hesabınız askıya alınmıştır.", 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success([
                'user'  => new UserResource($user),
                'token' => $token,
            ], "Giriş başarılı.");

        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422, $e->errors());
        } catch (\Throwable $e) {
            \Log::error($e);
            return ApiResponse::error("Giriş sırasında bir hata oluştu.", 500);
        }
    }

    public function me(Request $request)
    {
        return ApiResponse::success(new UserResource($request->user()), "Kullanıcı bilgisi");
    }

    public function profile(Request $request)
    {
        try {
            $user = $this->userService->getProfile($request->user()->id);
            return ApiResponse::success(new UserResource($user), "Profil getirildi");
        } catch (\Throwable $e) {
            \Log::error($e);
            return ApiResponse::error("Profil getirilemedi.", 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('avatar')) {
                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            }
            $user = $this->userService->updateProfile($request->user()->id, $data);
            return ApiResponse::success(new UserResource($user), "Profil güncellendi");
        } catch (\Throwable $e) {
            \Log::error($e);
            return ApiResponse::error("Profil güncellenemedi.", 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Çıkış başarılı.']);
    }
}
