<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SetLocaleFromHeader
{
    public function handle($request, Closure $next)
    {
        $lang = $request->header('X-Locale', 'tr');
        $lang = (string) Str::of($lang)->lower()->substr(0, 2);
        app()->setLocale(in_array($lang, ['tr', 'en'], true) ? $lang : 'tr');

        return $next($request);
    }
}
