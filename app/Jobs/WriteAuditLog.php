<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class WriteAuditLog implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        AuditLog::create($this->payload);
    }
}
