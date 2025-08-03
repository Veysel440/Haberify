<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Not: $this->route('tag') parametresi route'daki isimle aynı olmalı.
        // Route::put('/tags/{tag}') ise, 'tag' doğru olur.
        return [
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $this->route('tag'),
            'slug' => 'sometimes|required|string|max:255|unique:tags,slug,' . $this->route('tag'),
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
