<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
