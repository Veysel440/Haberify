<?php

declare(strict_types=1);

namespace App\Services;

class VirusScanner
{
    public function isClean(string $filepath): bool
    {
        if (config('virus.driver') !== 'clamav') return true;

        $fp = @fopen($filepath, 'rb');
        if (!$fp) return false;

        $sock = @fsockopen(config('virus.clamav.host'), (int)config('virus.clamav.port'), $errno, $errstr, (int)config('virus.clamav.timeout'));
        if (!$sock) { fclose($fp); return true; }

        stream_set_timeout($sock, (int)config('virus.clamav.timeout'));
        fwrite($sock, "zINSTREAM\0");
        $chunkSize = (int)config('virus.clamav.max_chunk', 8192);
        while (!feof($fp)) {
            $chunk = fread($fp, $chunkSize);
            $len = pack('N', strlen($chunk));
            fwrite($sock, $len.$chunk);
        }
        fwrite($sock, pack('N', 0));
        $result = fgets($sock);
        fclose($sock);
        fclose($fp);


        return is_string($result) && str_contains($result, 'OK') && !str_contains($result, 'FOUND');
    }
}
