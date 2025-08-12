<?php

namespace App\Http\Requests\Admin\Article;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('articles.publish') ?? false; }
    public function rules(): array {
        return [
            'scheduled_at' => 'required|date|after:now',
        ];
    }
}
