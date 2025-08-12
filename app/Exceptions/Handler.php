<?php

namespace App\Exceptions;

class Handler
{
    public function register(): void
    {
        $this->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                $code = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                return response()->json([
                    'status'  => 'error',
                    'message' => $e->getMessage() ?: 'Server Error',
                ], $code);
            }
        });
    }

}
