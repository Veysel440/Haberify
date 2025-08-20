<?php

namespace App\Http\Middleware;


use App\Services\AuditLogger;
use Closure;
use Illuminate\Http\Request;

class AuditMutation
{
    public function __construct(private AuditLogger $audit) {}

    public function handle(Request $request, Closure $next, string $action)
    {
        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            $routeName = optional($request->route())->getName();
            $this->audit->log($action, null, ['route' => $routeName]);
        }
        return $response;
    }
}
