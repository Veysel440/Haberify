<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'scheduled_at' => 'nullable|date|after:now',
            'video' => 'nullable|url|max:255',
        ];
    }
}
