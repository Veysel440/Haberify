<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reason' => 'nullable|string|max:1000'
        ];
    }
}
