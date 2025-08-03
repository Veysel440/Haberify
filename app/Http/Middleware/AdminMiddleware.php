<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {

            Log::warning('Admin middleware: Yetkisiz erişim', [
                'user_id' => $user?->id,
                'ip'      => $request->ip(),
                'url'     => $request->fullUrl(),
            ]);
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim.'], 403);
        }

        return $next($request);
    }
}
