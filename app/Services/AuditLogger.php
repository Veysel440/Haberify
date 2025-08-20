<?php

namespace App\Services;

use App\Jobs\WriteAuditLog;
use Illuminate\Http\Request;

class AuditLogger
{
    public function __construct(private Request $req) {}

    public function log(string $action, $target = null, array $meta = []): void
    {
        $payload = [
            'user_id' => optional($this->req->user())->id,
            'action'  => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id'   => $target->id ?? null,
            'ip'     => $this->req->ip(),
            'ua'     => substr((string)$this->req->header('User-Agent'),0,255),
            'route'  => optional($this->req->route())->getName(),
            'meta'   => $meta,
        ];
        WriteAuditLog::dispatch($payload)->onQueue('audit');
    }
}
