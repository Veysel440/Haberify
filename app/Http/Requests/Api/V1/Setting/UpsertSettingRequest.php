<?php

namespace App\Http\Requests\Api\V1\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSettingRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('settings.manage') ?? false; }
    public function rules(): array
    {
        return [
            'value' => 'required|array',
        ];
    }
}
