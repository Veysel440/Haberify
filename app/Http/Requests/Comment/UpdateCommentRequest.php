<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Yorum metni zorunludur.',
            'body.string'   => 'Yorum metni geçersiz.',
            'body.max'      => 'Yorum en fazla 1000 karakter olabilir.',
        ];
    }
}
