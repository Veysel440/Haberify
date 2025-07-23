<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'user'      => new UserResource($this->user),
            'body'      => $this->body,
            'parent_id' => $this->parent_id,
            'replies'   => CommentResource::collection($this->whenLoaded('replies')),
            'created_at'=> $this->created_at->toDateTimeString(),

            'like_stats' => $this->whenLoaded('likes', function() {
                return [
                    'likes'    => $this->likes->where('is_like', true)->count(),
                    'dislikes' => $this->likes->where('is_like', false)->count(),
                ];
            }),
        ];
    }
}
