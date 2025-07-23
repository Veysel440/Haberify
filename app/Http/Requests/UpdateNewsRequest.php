<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'       => 'sometimes|required|string|max:255',
            'slug'        => 'sometimes|required|string|max:255|unique:news,slug,' . $this->route('news'),
            'content'     => 'sometimes|required|string',
            'image'       => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
        ];
    }
}
