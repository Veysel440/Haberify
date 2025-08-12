<?php

namespace App\Http\Requests\Api\V1\Page;

use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('pages.manage') ?? false; }
    public function rules(): array {
        return [
            'title'=>'required|string|max:160',
            'slug'=>'nullable|string|max:180|unique:pages,slug',
            'body'=>'nullable|string',
            'meta'=>'array',
            'status'=>'in:draft,published',
        ];
    }
}
