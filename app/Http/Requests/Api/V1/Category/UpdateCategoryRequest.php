<?php

namespace App\Http\Requests\Api\V1\Category;


use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('categories.manage') ?? false; }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'name'        => 'sometimes|required|string|max:120',
            'slug'        => 'sometimes|nullable|string|max:140|unique:categories,slug,'.$id,
            'description' => 'sometimes|nullable|string',
            'parent_id'   => 'sometimes|nullable|exists:categories,id',
            'is_active'   => 'sometimes|boolean',
        ];
    }
}
