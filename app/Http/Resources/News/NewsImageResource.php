<?php

namespace App\Http\Resources\News;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsImageResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'image' => $this->image ? url('storage/' . $this->image) : null,
        ];
    }
}
