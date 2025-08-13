<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class ValidateJson
{
    public function handle($request, Closure $next)
    {
        if (in_array($request->method(), ['POST','PUT','PATCH']) &&
            str_starts_with($request->header('Content-Type',''), 'application/json')) {
            json_decode($request->getContent());
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['status'=>'error','message'=>'Geçersiz JSON'], 422);
            }
        }
        return $next($request);
    }
}
