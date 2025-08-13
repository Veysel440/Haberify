<?php

return [
    'driver' => env('VIRUS_SCAN_DRIVER', 'clamav'),
    'clamav' => [
        'host' => env('CLAMAV_HOST', '127.0.0.1'),
        'port' => (int) env('CLAMAV_PORT', 3310),
        'timeout' => 10,
        'max_chunk' => 8192,
    ],
];
