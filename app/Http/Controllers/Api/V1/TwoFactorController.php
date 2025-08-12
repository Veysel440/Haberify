<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(){ $this->middleware('auth:sanctum')->only(['enable','disable','qrcode']); }

    public function enable(Request $r)
    {
        $u = $r->user();
        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();

        $u->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(collect(range(1,8))->map(fn()=>\Str::random(10))->all())),
            'two_factor_confirmed_at' => null,
        ])->save();

        return response()->json(['data'=>[
            'secret'=>$secret,
            'otpauth_url'=>$g2fa->getQRCodeUrl(config('app.name'), $u->email, $secret),
        ]],201);
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
        if (!auth()->validate($data)) return response()->json(['message'=>'Invalid credentials'],422);

        $u = \App\Models\User::where('email',$data['email'])->firstOrFail();

        if (!$u->two_factor_secret) {
            auth()->attempt($data);
            return response()->json(['data'=>['token'=>$u->createToken('api')->plainTextToken]]);
        }

        return response()->json(['data'=>['requires_2fa'=>true,'tmp'=>encrypt(['id'=>$u->id,'ts'=>now()->timestamp])]]);
    }

    public function verifyCode(Request $r)
    {
        $d = $r->validate(['tmp'=>'required','code'=>'required|string']);
        $payload = decrypt($d['tmp']);
        $u = \App\Models\User::findOrFail($payload['id']);

        $secret = decrypt($u->two_factor_secret);
        $g2fa = new Google2FA();

        if (!$g2fa->verifyKey($secret, $d['code'])) {
            return response()->json(['message'=>'Invalid 2FA code'],422);
        }

        $u->forceFill(['two_factor_confirmed_at'=>now()])->save();
        return response()->json(['data'=>['token'=>$u->createToken('api')->plainTextToken]]);
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
