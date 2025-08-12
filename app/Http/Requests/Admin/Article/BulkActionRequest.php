<?php

namespace App\Http\Requests\Admin\Article;

use Illuminate\Foundation\Http\FormRequest;

class BulkActionRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('articles.update') ?? false; }
    public function rules(): array {
        return [
            'ids'   => 'required|array|min:1|max:200',
            'ids.*' => 'integer|exists:articles,id',
            'action'=> 'required|string|in:publish,unpublish,feature,unfeature,delete',
        ];
    }
}
