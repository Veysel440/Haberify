<?php

namespace App\Http\Requests\Api\V1\Article;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('articles.create') ?? false; }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:200',
            'slug'        => 'nullable|string|max:220|unique:articles,slug',
            'summary'     => 'nullable|string|max:500',
            'body'        => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tag_ids'     => 'array',
            'tag_ids.*'   => 'integer|exists:tags,id',
            'is_featured' => 'boolean',
            'language'    => 'in:tr,en',
        ];
    }
}
