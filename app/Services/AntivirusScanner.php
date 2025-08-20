<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AntivirusScanner
{
    public function assertClean(string $disk, string $path): void
    {
        $abs = Storage::disk($disk)->path($path);
        $cmd = sprintf('clamscan -i --no-summary %s 2>/dev/null', escapeshellarg($abs));
        $out = [];
        $code = 0;
        exec($cmd, $out, $code);
        if ($code === 1) {
            throw new RuntimeException("infected: ".$path);
        }
    }
}
