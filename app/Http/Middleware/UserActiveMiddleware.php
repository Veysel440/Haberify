<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class UserActiveMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->status === 'banned') {
            return response()->json(['message' => 'Hesabınız askıya alınmıştır.'], 403);
        }
        return $next($request);
    }
}
