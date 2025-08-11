<?php

namespace App\Http\Requests\Api\V1\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    { return true; }

    public function rules(): array
    {
        return [
            'body'       => 'required|string|min:3|max:2000',
            'guest_name' => 'nullable|string|max:80',
        ];
    }
}
