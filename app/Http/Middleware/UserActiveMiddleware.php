<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class UserActiveMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Yetkisiz erişim!'], 401);
        }

        if (isset($user->suspended_until) && $user->suspended_until) {
            if (Carbon::parse($user->suspended_until)->isFuture()) {
                Log::notice('Askıya alınan kullanıcı erişim denemesi.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->json([
                    'message' => 'Hesabınız ' . Carbon::parse($user->suspended_until)->translatedFormat('d M Y H:i') . ' tarihine kadar askıya alınmıştır.'
                ], 403);
            }
        }

        switch ($user->status) {
            case 'banned':
                Log::notice('Banlı kullanıcı erişim denemesi.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);
                return response()->json([
                    'message' => 'Hesabınız kalıcı olarak askıya alınmıştır.'
                ], 403);

            case 'pending':
                return response()->json([
                    'message' => 'Hesabınız henüz onaylanmamış, lütfen e-posta adresinizi doğrulayın.'
                ], 403);

            case 'passive':
                return response()->json([
                    'message' => 'Hesabınız devre dışı bırakılmıştır. Lütfen destek ile iletişime geçin.'
                ], 403);
        }

        return $next($request);
    }
}
