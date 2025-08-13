<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        //
    }

    public function enable(Request $r)
    {
        $u = $r->user();
        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();

        $u->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1,8))->map(fn()=>Str::random(10))->all())),
            'two_factor_confirmed_at' => null,
        ])->save();

        return response()->json(['data'=>[
            'secret'=>$secret,
            'otpauth_url'=>$g2fa->getQRCodeUrl(config('app.name'), $u->email, $secret),
        ]], 201);
    }

    public function qrcode(Request $r)
    {
        $u = $r->user();
        abort_if(!$u->two_factor_secret, 404);
        $secret = decrypt($u->two_factor_secret);
        $g2fa = new Google2FA();
        return response()->json(['data'=>[
            'otpauth_url'=>$g2fa->getQRCodeUrl(config('app.name'), $u->email, $secret),
        ]]);
    }

    public function verifyLogin(Request $r)
    {
        $data = $r->validate(['email'=>'required|email','password'=>'required']);
        $key = 'login:'.Str::lower($data['email']).':'.$r->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['message'=>'Çok fazla deneme. Daha sonra deneyin.'], 429);
        }

        if (!auth()->validate($data)) {
            RateLimiter::hit($key, 60);
            return response()->json(['message'=>'Geçersiz bilgiler'], 422);
        }
        RateLimiter::clear($key);

        $u = \App\Models\User::where('email',$data['email'])->firstOrFail();

        if (!$u->two_factor_secret) {
            auth()->attempt($data);
            $abilities = $u->hasRole('admin') ? ['*'] : ['articles:read','comments:create','me:read'];
            return response()->json(['data'=>['token'=>$u->createToken('api', $abilities)->plainTextToken]]);
        }

        return response()->json(['data'=>[
            'requires_2fa'=>true,
            'tmp'=>encrypt(['id'=>$u->id,'ts'=>now()->timestamp])
        ]]);
    }

    public function verifyCode(Request $r)
    {
        $d = $r->validate(['tmp'=>'required','code'=>'required|string']);
        $payload = decrypt($d['tmp']);
        $u = \App\Models\User::findOrFail($payload['id']);

        $key = '2fa:'.$u->id.':'.$r->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['message'=>'2FA kilitlendi, sonra deneyin'], 429);
        }

        $secret = decrypt($u->two_factor_secret);
        $g2fa = new Google2FA();

        if (!$g2fa->verifyKey($secret, $d['code'])) {
            RateLimiter::hit($key, 60);
            return response()->json(['message'=>'Kod hatalı'], 422);
        }
        RateLimiter::clear($key);

        $u->forceFill(['two_factor_confirmed_at'=>now()])->save();
        $abilities = $u->hasRole('admin') ? ['*'] : ['articles:read','comments:create','me:read'];
        return response()->json(['data'=>['token'=>$u->createToken('api', $abilities)->plainTextToken]]);
    }

    public function disable(Request $r)
    {
        $u = $r->user();
        $u->forceFill([
            'two_factor_secret'=>null,
            'two_factor_recovery_codes'=>null,
            'two_factor_confirmed_at'=>null,
        ])->save();
        return response()->noContent();
    }
}
