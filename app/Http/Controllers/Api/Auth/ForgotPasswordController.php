<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Exception;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);
            $status = Password::sendResetLink($request->only('email'));

            if ($status == Password::RESET_LINK_SENT) {
                return response()->json([
                    'message' => 'Sıfırlama linki e-posta adresinize gönderildi.'
                ], 200);
            }

            return response()->json([
                'message' => 'E-posta bulunamadı veya gönderilemedi.'
            ], 404);
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
