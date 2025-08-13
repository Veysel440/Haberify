<?php

namespace App\Http\Middleware;

use Closure;

class SentryContext
{
    public function handle($request, Closure $next)
    {
        if (app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($request) {
                $scope->setTag('route', optional($request->route())->getName() ?? $request->path());
                $scope->setTag('request_id', app('log')->getLogger()?->getProcessors() ? 'set' : 'na');
                if ($u = $request->user()) {
                    $scope->setUser(['id'=>$u->id, 'email'=>$u->email]);
                }
            });
        }
        return $next($request);
    }
}
