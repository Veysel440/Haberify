<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserServiceInterface;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
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

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }


    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
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
        }
        catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 422, $e->errors());
        }
        catch (\Exception $e) {
            return ApiResponse::error("Giriş sırasında bir hata oluştu.", 500);
        }
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function profile(Request $request)
    {
        try {
            $user = $this->userService->getProfile($request->user()->id);
            return ApiResponse::success(new UserResource($user), "Profil getirildi");
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return ApiResponse::error("Profil güncellenemedi.", 500);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Çıkış başarılı.']);
    }
}
