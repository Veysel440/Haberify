<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'user'       => new UserResource($this->user),
            'body'       => $this->body,
            'parent_id'  => $this->parent_id,
            'replies'    => CommentResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'like_stats' => $this->whenLoaded('likes', function () {
                return [
                    'likes'    => $this->likes->where('is_like', true)->count(),
                    'dislikes' => $this->likes->where('is_like', false)->count(),
                ];
            }),
        ];
    }
}
