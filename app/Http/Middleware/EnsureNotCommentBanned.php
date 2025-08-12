<?php

namespace App\Http\Middleware;

use Closure;

class EnsureNotCommentBanned
{
    public function handle($request, Closure $next)
    {
        $u = $request->user();
        if ($u && $u->is_comment_banned) {
            $until = optional($u->comment_banned_until)->toDateTimeString();
            abort(403, 'Yorum yapma yasağınız var'.($until ? " (bitiş: {$until})" : ''));
        }
        return $next($request);
    }
}
