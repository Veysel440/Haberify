<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->save();
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return response()->json([
                    'message' => 'Şifre başarıyla sıfırlandı!'
                ]);
            }

            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            \Log::error($e);
            return response()->json([
                'message' => 'Bir hata oluştu. Lütfen tekrar deneyiniz.'
            ], 500);
        }
    }
}
