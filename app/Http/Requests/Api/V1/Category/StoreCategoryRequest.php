<?php

namespace App\Http\Requests\Api\V1\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('categories.manage') ?? false; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:120',
            'slug'        => 'nullable|string|max:140|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
        ];
    }
}
