<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Shared base for auth-flow form requests.
 *
 * Exposes a typed `rules()` contract so callers (and static analysis) can
 * rely on it without having to discriminate between concrete subclasses.
 * Laravel itself calls `rules()` dynamically on any FormRequest; this base
 * merely formalises the signature.
 */
abstract class AuthFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    abstract public function rules(): array;
}
