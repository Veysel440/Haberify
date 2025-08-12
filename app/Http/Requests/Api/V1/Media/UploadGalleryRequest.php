<?php

namespace App\Http\Requests\Api\V1\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadGalleryRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('articles.update') ?? false; }
    public function rules(): array
    {
        return [
            'files'   => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|max:8192',
        ];
    }
}
