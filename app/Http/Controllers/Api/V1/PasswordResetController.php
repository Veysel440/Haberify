<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function sendLink(Request $r)
    {
        $r->validate(['email'=>'required|email']);
        $status = Password::sendResetLink($r->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message'=>__($status)])
            : response()->json(['message'=>__($status)], 422);
    }

    public function reset(Request $r)
    {
        $r->validate([
            'token'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed|min:8'
        ]);

        $status = Password::reset(
            $r->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->forceFill(['password'=>bcrypt($password),'remember_token'=>Str::random(60)])->save();
                event(new PasswordReset($user));
                // Tüm tokenları iptal et
                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message'=>__($status)])
            : response()->json(['message'=>__($status)], 422);
    }
}
