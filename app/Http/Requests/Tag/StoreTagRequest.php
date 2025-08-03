<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name',
            'slug' => 'required|string|max:255|unique:tags,slug'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Etiket adı zorunludur.',
            'name.unique'   => 'Bu etiket adı zaten kullanılıyor.',
            'name.max'      => 'Etiket adı en fazla 255 karakter olabilir.',
            'slug.required' => 'Slug alanı zorunludur.',
            'slug.unique'   => 'Bu slug zaten kullanılıyor.',
            'slug.max'      => 'Slug en fazla 255 karakter olabilir.',
        ];
    }
}
