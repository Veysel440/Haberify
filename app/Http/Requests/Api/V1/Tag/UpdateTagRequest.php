<?php

namespace App\Http\Requests\Api\V1\Tag;


use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('tags.manage') ?? false; }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'name'      => 'sometimes|required|string|max:80',
            'slug'      => 'sometimes|nullable|string|max:100|unique:tags,slug,'.$id,
            'is_active' => 'sometimes|boolean',
        ];
    }
}
