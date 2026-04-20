<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Audit;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Query-string validator for `GET /api/v1/audit`.
 *
 * Supported filters:
 *   log_name   — kategori / tag / comment / user / setting / ...
 *                (yalnızca projenin izlediği log adlarını kabul eder)
 *   causer_id  — belirli bir kullanıcının eylemleri
 *   from / to  — tarih aralığı (Y-m-d veya ISO 8601)
 *   per_page   — sayfa başına kayıt (1..100)
 */
final class AuditIndexRequest extends FormRequest
{
    /**
     * Route middleware `permission:analytics.view` yetkilendirmeyi
     * halleder; burada ek kontrol yok.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'log_name' => ['nullable', 'string', 'in:' . implode(',', self::KNOWN_LOG_NAMES)],
            'causer_id' => ['nullable', 'integer', 'min:1'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Spatie Activitylog log_name'lerinin allowlist'i. Yeni bir model'e
     * `LogsActivity` trait'i eklendiğinde bu liste güncellenir —
     * random string'ler filtreye kaçmaz.
     *
     * @var list<string>
     */
    public const KNOWN_LOG_NAMES = [
        'haberify',   // config default
        'article',
        'category',
        'tag',
        'comment',
        'page',
        'menu',
        'setting',
        'user',
    ];

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? 50);
    }

    public function logName(): ?string
    {
        $v = $this->validated('log_name');

        return $v === null ? null : (string) $v;
    }

    public function causerId(): ?int
    {
        $v = $this->validated('causer_id');

        return $v === null ? null : (int) $v;
    }

    public function from(): ?string
    {
        $v = $this->validated('from');

        return $v === null ? null : (string) $v;
    }

    public function to(): ?string
    {
        $v = $this->validated('to');

        return $v === null ? null : (string) $v;
    }
}
