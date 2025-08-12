<?php

namespace App\Http\Requests\Api\V1\Menu;

use Illuminate\Foundation\Http\FormRequest;

class UpsertMenuRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('menus.edit') ?? false; }
    public function rules(): array
    {
        return [
            'items' => 'required|array|max:50',
            'items.*.title' => 'required|string|max:80',
            'items.*.url'   => 'required|string|max:255',
            'items.*.target'=> 'nullable|in:_self,_blank',
            'items.*.children'=>'array|max:20'
        ];
    }
}
