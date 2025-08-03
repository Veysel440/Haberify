<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string', 'min:20'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'category_id'  => ['required', 'exists:categories,id'],
            'tags'         => ['nullable', 'array'],
            'tags.*'       => ['integer', 'exists:tags,id'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
            'video'        => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Başlık alanı zorunludur.',
            'title.max'             => 'Başlık en fazla :max karakter olabilir.',
            'content.required'      => 'İçerik alanı zorunludur.',
            'content.min'           => 'İçerik en az :min karakter olmalıdır.',
            'image.image'           => 'Yüklenen dosya bir görsel olmalıdır.',
            'image.mimes'           => 'Görsel sadece jpeg, png, jpg, gif, webp formatında olmalıdır.',
            'image.max'             => 'Görsel boyutu en fazla :max kilobyte olabilir.',
            'category_id.required'  => 'Kategori seçimi zorunludur.',
            'category_id.exists'    => 'Seçilen kategori bulunamadı.',
            'tags.array'            => 'Etiketler bir dizi olmalıdır.',
            'tags.*.exists'         => 'Seçili etiket(ler) bulunamadı.',
            'scheduled_at.date'     => 'Geçerli bir tarih giriniz.',
            'scheduled_at.after'    => 'Planlanan tarih bugünden sonra olmalıdır.',
            'video.url'             => 'Geçerli bir video adresi giriniz.',
            'video.max'             => 'Video adresi çok uzun.',
        ];
    }
}
