<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:categories,slug',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Kategori adı zorunludur.',
            'name.max'       => 'Kategori adı en fazla 255 karakter olabilir.',
            'slug.required'  => 'Slug alanı zorunludur.',
            'slug.unique'    => 'Bu slug zaten kullanılıyor.',
            'image.image'    => 'Yüklediğiniz dosya bir görsel olmalıdır.',
            'image.mimes'    => 'Sadece jpeg, png, jpg, gif, webp formatları kabul edilir.',
            'image.max'      => 'Görsel maksimum 4MB olabilir.',
        ];
    }
}
