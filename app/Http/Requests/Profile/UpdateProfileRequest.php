<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => 'sometimes|required|string|max:255',
            'bio'      => 'nullable|string|max:1000',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Adınızı belirtmelisiniz.',
            'name.string'       => 'Ad alanı metin olmalı.',
            'name.max'          => 'Adınız en fazla 255 karakter olabilir.',
            'bio.string'        => 'Biyografi metin olmalı.',
            'bio.max'           => 'Biyografi en fazla 1000 karakter olabilir.',
            'avatar.image'      => 'Profil fotoğrafı görsel olmalı.',
            'avatar.mimes'      => 'Profil fotoğrafı jpeg, png, jpg, gif veya webp olmalı.',
            'avatar.max'        => 'Profil fotoğrafı en fazla 4MB olabilir.',
            'password.string'   => 'Şifre metin olmalı.',
            'password.min'      => 'Şifre en az 6 karakter olmalı.',
            'password.confirmed'=> 'Şifreler eşleşmiyor.',
        ];
    }
}
