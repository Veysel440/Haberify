<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Validation\Rules\Password;

final class ResetPasswordRequest extends AuthFormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email:rfc,strict', 'max:254'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }
}
