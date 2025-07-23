<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'news_id' => 'required|exists:news,id',
            'body'    => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ];
    }
}
