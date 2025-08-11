<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'=>$this->id,
            'body'=>$this->body,
            'author'=>$this->user?->name ?? $this->guest_name,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
        ];
    }
}
