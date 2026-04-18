<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\TwoFactor;

use Illuminate\Foundation\Http\FormRequest;

final class VerifyLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'string'],
        ];
    }
}
