<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler
{
    public function register(): void
    {
        $this->renderable(function (\Throwable $e, $request) {
            if (!$request->expectsJson()) return null;

            if ($e instanceof ValidationException) {
                return ApiResponse::error('Doğrulama hatası', ApiErrorCode::VALIDATION->value, 422)
                    ->toResponse($request)->setData([
                        'success'=>false,
                        'error'=>['message'=>'Doğrulama hatası','code'=>ApiErrorCode::VALIDATION->value,'details'=>$e->errors()]
                    ]);
            }

            $status = 500; $code = ApiErrorCode::SERVER->value; $msg = 'Sunucu hatası';
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                [$code,$msg] = match ($status) {
                    401 => [ApiErrorCode::UNAUTH->value, 'Yetkisiz'],
                    403 => [ApiErrorCode::FORBIDDEN->value, 'Erişim yok'],
                    404 => [ApiErrorCode::NOT_FOUND->value, 'Bulunamadı'],
                    409 => [ApiErrorCode::CONFLICT->value, 'Çakışma'],
                    429 => [ApiErrorCode::RATE_LIMIT->value, 'Hız limiti aşıldı'],
                    default => [ApiErrorCode::SERVER->value, $e->getMessage() ?: 'Hata'],
                };
            }

            if (!app()->isProduction()) { $msg = $e->getMessage() ?: $msg; }

            return ApiResponse::error($msg, $code, $status)->toResponse($request);
        });
    }

}
