<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'image' => $this->image ? url('storage/'.$this->image) : null,
        ];
    }
}
