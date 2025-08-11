<?php

namespace App\Exceptions;

class Handler
{
    public function register(): void
    {
        $this->renderable(function (\App\Exceptions\ApiException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], $e->getCode() ?: 400);
            }
        });
    }

}
