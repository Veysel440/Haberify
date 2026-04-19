<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

final class ForgotPasswordRequest extends AuthFormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,strict', 'max:254'],
        ];
    }
}
