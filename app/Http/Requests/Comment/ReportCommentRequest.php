<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class ReportCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'reason.string' => 'Şikayet sebebi metin olmalı.',
            'reason.max'    => 'Şikayet sebebi en fazla 1000 karakter olabilir.'
        ];
    }
}
