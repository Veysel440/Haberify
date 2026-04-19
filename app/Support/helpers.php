<?php

declare(strict_types=1);

/**
 * Global helper functions (autoloaded via composer.json -> autoload.files).
 *
 * These are intentionally in the GLOBAL namespace so callers across the app
 * can write `per_page($request)` without a `use function` import. The
 * `function_exists` guards make re-including the file idempotent.
 */

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

if (! function_exists('ok')) {
    /**
     * @param mixed $data
     */
    function ok($data = null, string $message = 'ok', int $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $code);
    }
}

if (! function_exists('err')) {
    /**
     * @param mixed $errors
     */
    function err(string $message = 'error', int $code = 400, $errors = null): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message, 'errors' => $errors], $code);
    }
}

if (! function_exists('page_payload')) {
    /**
     * @param LengthAwarePaginator<int, mixed> $paginator
     *
     * @return array<string, mixed>
     */
    function page_payload(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}

if (! function_exists('slugify')) {
    function slugify(?string $text, ?string $fallback = null): string
    {
        $s = trim((string) $text);

        if ($s === '') {
            $s = (string) $fallback;
        }

        return Str::slug((string) $s);
    }
}

if (! function_exists('estimate_minutes')) {
    function estimate_minutes(string $html, int $wpm = 200): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($html)) / $wpm));
    }
}

if (! function_exists('per_page')) {
    function per_page(Request $r, int $def = 15, int $max = 100): int
    {
        $p = (int) $r->query('per_page', $def);

        return max(1, min($max, $p));
    }
}

if (! function_exists('ref_host')) {
    function ref_host(?string $referer, ?string $appHost = null): ?string
    {
        if (! $referer) {
            return null;
        }
        $host = parse_url($referer, PHP_URL_HOST);

        if (! $host) {
            return null;
        }
        $app = $appHost ?: parse_url((string) config('app.url'), PHP_URL_HOST);

        return ($host === $app) ? 'direct' : $host;
    }
}
