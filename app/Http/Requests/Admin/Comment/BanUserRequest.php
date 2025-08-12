<?php

namespace App\Http\Requests\Admin\Comment;

use Illuminate\Foundation\Http\FormRequest;

class BanUserRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('comments.moderate') ?? false; }
    public function rules(): array {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'until'   => 'nullable|date|after:now',
            'reason'  => 'nullable|string|max:255',
        ];
    }
}
