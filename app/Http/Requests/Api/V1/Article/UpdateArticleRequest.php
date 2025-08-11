<?php

namespace App\Http\Requests\Api\V1\Article;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    { return $this->user()?->can('articles.update') ?? false; }

    public function rules(): array
    {
        $id = (int) $this->route('id');
        return [
            'title'       => 'sometimes|required|string|max:200',
            'slug'        => 'sometimes|nullable|string|max:220|unique:articles,slug,'.$id,
            'summary'     => 'sometimes|nullable|string|max:500',
            'body'        => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'tag_ids'     => 'sometimes|array',
            'tag_ids.*'   => 'integer|exists:tags,id',
            'is_featured' => 'sometimes|boolean',
            'language'    => 'sometimes|in:tr,en',
        ];
    }
}
