<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'news_id'   => 'required|exists:news,id',
            'body'      => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'news_id.required'   => 'Haber ID zorunludur.',
            'news_id.exists'     => 'Geçersiz haber ID.',
            'body.required'      => 'Yorum metni zorunludur.',
            'body.string'        => 'Yorum metni geçersiz.',
            'body.max'           => 'Yorum en fazla 1000 karakter olabilir.',
            'parent_id.exists'   => 'Yanıt verilen yorum bulunamadı.',
        ];
    }
}
