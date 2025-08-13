<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

final class ApiResponse implements Responsable
{
    public function __construct(
        private readonly bool $success,
        private readonly mixed $data = null,
        private readonly array $meta = [],
        private readonly ?string $error = null,
        private readonly ?string $code = null,
        private readonly int $status = 200
    ) {}

    public static function ok(mixed $data = null, array $meta = [], int $status = 200): self
    { return new self(true, $data, $meta, null, null, $status); }

    public static function created(mixed $data = null, array $meta = []): self
    { return new self(true, $data, $meta, null, null, 201); }

    public static function noContent(): JsonResponse
    { return response()->json(null, 204); }

    public static function error(string $message, string $code, int $status): self
    { return new self(false, null, [], $message, $code, $status); }

    public function toResponse($request): JsonResponse
    {
        $payload = ['success'=>$this->success];
        if ($this->success) {
            $payload['data'] = $this->data;
            if ($this->meta) $payload['meta'] = $this->meta;
        } else {
            $payload['error'] = ['message'=>$this->error, 'code'=>$this->code];
        }
        return response()->json($payload, $this->status);
    }
}
