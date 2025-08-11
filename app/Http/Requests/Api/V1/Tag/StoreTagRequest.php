<?php

namespace App\Http\Requests\Api\V1\Tag;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('tags.manage') ?? false; }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:80',
            'slug'      => 'nullable|string|max:100|unique:tags,slug',
            'is_active' => 'boolean',
        ];
    }
}
