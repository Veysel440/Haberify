<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsImageRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:4096',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Görsel zorunludur.',
            'image.image'    => 'Dosya tipiniz geçersiz.',
            'image.mimes'    => 'Yalnızca jpeg, jpg, png, gif, webp dosyalarına izin verilir.',
            'image.max'      => 'Görsel boyutu en fazla 4MB olmalı.',
        ];
    }
}
