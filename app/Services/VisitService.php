<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Facades\Cache;

class VisitService
{
    public function record(array $d): void
    {
        $sig = sprintf('visit:%s:%s:%s', $d['session_id'] ?? 'anon', $d['path'], $d['ref'] ?? 'direct');
        if (!Cache::add($sig, 1, now()->addMinutes(5))) return;
        Visit::create($d);
    }
}
