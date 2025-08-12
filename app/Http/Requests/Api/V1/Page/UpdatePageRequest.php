<?php

namespace App\Http\Requests\Api\V1\Page;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('pages.manage') ?? false; }
    public function rules(): array {
        $id = (int)$this->route('id');
        return [
            'title'=>'sometimes|required|string|max:160',
            'slug'=>"sometimes|nullable|string|max:180|unique:pages,slug,{$id}",
            'body'=>'sometimes|nullable|string',
            'meta'=>'sometimes|array',
            'status'=>'sometimes|in:draft,published',
        ];
    }
}
